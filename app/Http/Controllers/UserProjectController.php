<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use App\Services\CloudUploadIntent;
use App\Services\ProjectFileManager;
use App\Services\ProjectMetadataSuggester;
use App\Services\ProjectTagResolver;

class UserProjectController extends Controller
{
    public function create()
    {
        return view('projects.create', $this->formData());
    }

    public function store(StoreProjectRequest $request, ProjectFileManager $files, ProjectTagResolver $tags, ProjectMetadataSuggester $metadata)
    {
        $validated = $request->validated();
        $tagIds = $tags->resolve($validated['tags'] ?? [], $validated['new_tags'] ?? null);
        unset($validated['tags'], $validated['new_tags']);

        $fileData = filled($validated['cloud_pdf_url'] ?? null)
            ? $files->storeCloudUpload($validated)
            : $files->store($request->file('pdf_file'));
        $payload = $this->projectPayload($validated);
        $suggestions = $metadata->suggest($payload, $fileData['pdf_text'] ?? '', $tagIds);

        $project = Project::create(array_merge(
            $payload,
            $suggestions['payload'],
            $fileData,
            ['uploaded_by' => $request->user()->id]
        ));

        $project->tags()->sync(collect($tagIds)->merge($suggestions['tag_ids'])->unique()->values()->all());

        return redirect()->route('projects.show', $project)->with('success', 'Project uploaded successfully.');
    }

    public function edit(Project $project)
    {
        abort_unless($project->canBeEditedBy(request()->user()), 403);

        $project->load('tags');

        return view('projects.edit', array_merge($this->formData(), compact('project')));
    }

    public function update(UpdateProjectRequest $request, Project $project, ProjectFileManager $files, ProjectTagResolver $tags, ProjectMetadataSuggester $metadata)
    {
        $validated = $request->validated();
        $tagIds = $tags->resolve($validated['tags'] ?? [], $validated['new_tags'] ?? null);
        unset($validated['tags'], $validated['new_tags']);

        $payload = $this->projectPayload($validated);
        $pdfText = $project->pdf_text;

        if (filled($validated['cloud_pdf_url'] ?? null)) {
            $files->delete($project);
            $fileData = $files->storeCloudUpload($validated, $project);
            $payload = array_merge($payload, $fileData);
            $pdfText = $fileData['pdf_text'] ?? '';
        } elseif ($request->hasFile('pdf_file')) {
            $files->delete($project);
            $fileData = $files->store($request->file('pdf_file'), $project);
            $payload = array_merge($payload, $fileData);
            $pdfText = $fileData['pdf_text'] ?? '';
        }

        $suggestions = $metadata->suggest($payload, $pdfText, $tagIds);

        $project->update(array_merge($payload, $suggestions['payload']));
        $project->tags()->sync(collect($tagIds)->merge($suggestions['tag_ids'])->unique()->values()->all());

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(),
            'cloudUpload' => app(CloudUploadIntent::class)->formConfig(request()->user()),
            'tags' => Tag::query()->orderBy('name')->get(),
        ];
    }

    private function projectPayload(array $validated): array
    {
        return [
            'category_id' => $validated['category_id'] ?? null,
            'student_name' => $validated['student_name'],
            'supervisor' => $validated['supervisor'] ?? null,
            'title' => $validated['title'],
            'project_type' => $validated['project_type'] ?? null,
            'abstract' => $validated['abstract'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
            'completion_year' => $validated['completion_year'],
        ];
    }
}
