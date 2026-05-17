<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use App\Services\ProjectFileManager;
use App\Services\ProjectMetadataSuggester;
use App\Services\ProjectTagResolver;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->with(['category', 'tags', 'uploader'])
            ->filtered($request->query())
            ->sorted($request->query('sort'))
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();
        $tags = Tag::query()->orderBy('name')->get();
        $projectTypes = Project::query()
            ->whereNotNull('project_type')
            ->distinct()
            ->orderBy('project_type')
            ->pluck('project_type');

        return view('admin.projects.index', compact('projects', 'categories', 'tags', 'projectTypes'));
    }

    public function create()
    {
        return view('admin.projects.create', $this->formData());
    }

    public function store(StoreProjectRequest $request, ProjectFileManager $files, ProjectTagResolver $tags, ProjectMetadataSuggester $metadata)
    {
        $validated = $request->validated();
        $tagIds = $tags->resolve($validated['tags'] ?? [], $validated['new_tags'] ?? null);
        unset($validated['tags'], $validated['new_tags']);
        $fileData = $files->store($request->file('pdf_file'));
        $payload = $this->projectPayload($validated);
        $suggestions = $metadata->suggest($payload, $fileData['pdf_text'] ?? '', $tagIds);

        $project = Project::create(array_merge(
            $payload,
            $suggestions['payload'],
            $fileData,
            ['uploaded_by' => $request->user()->id]
        ));

        $project->tags()->sync(collect($tagIds)->merge($suggestions['tag_ids'])->unique()->values()->all());

        return redirect()->route('admin.projects.edit', $project)->with('success', 'Project uploaded successfully.');
    }

    public function edit(Project $project)
    {
        $project->load('tags');

        return view('admin.projects.edit', array_merge($this->formData(), compact('project')));
    }

    public function update(UpdateProjectRequest $request, Project $project, ProjectFileManager $files, ProjectTagResolver $tags, ProjectMetadataSuggester $metadata)
    {
        $validated = $request->validated();
        $tagIds = $tags->resolve($validated['tags'] ?? [], $validated['new_tags'] ?? null);
        unset($validated['tags'], $validated['new_tags']);

        $payload = $this->projectPayload($validated);
        $pdfText = $project->pdf_text;

        if ($request->hasFile('pdf_file')) {
            $files->delete($project);
            $fileData = $files->store($request->file('pdf_file'), $project);
            $payload = array_merge($payload, $fileData);
            $pdfText = $fileData['pdf_text'] ?? '';
        }

        $suggestions = $metadata->suggest($payload, $pdfText, $tagIds);
        $payload = array_merge($payload, $suggestions['payload']);

        $project->update($payload);
        $project->tags()->sync(collect($tagIds)->merge($suggestions['tag_ids'])->unique()->values()->all());

        return redirect()->route('admin.projects.edit', $project)->with('success', 'Project updated successfully.');
    }

    public function destroy(Request $request, Project $project)
    {
        abort_unless($request->user()->canDeleteProjects(), 403);

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(),
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
