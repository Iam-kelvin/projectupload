<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use App\Services\ProjectFileManager;
use App\Services\ProjectTagResolver;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 50);

        $projects = Project::query()
            ->with(['category', 'tags', 'uploader'])
            ->filtered($request->query())
            ->sorted($request->query('sort'))
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($projects->through(fn (Project $project) => $this->projectPayload($project)));
    }

    public function show(Project $project)
    {
        $project->load(['category', 'tags', 'uploader']);

        return response()->json(['data' => $this->projectPayload($project, true)]);
    }

    public function store(StoreProjectRequest $request, ProjectFileManager $files, ProjectTagResolver $tags)
    {
        $validated = $request->validated();
        $tagIds = $tags->resolve($validated['tags'] ?? [], $validated['new_tags'] ?? null);
        unset($validated['tags'], $validated['new_tags']);

        $project = Project::create(array_merge(
            $this->projectPayloadFromRequest($validated),
            $files->store($request->file('pdf_file')),
            ['uploaded_by' => $request->user()->id]
        ));
        $project->tags()->sync($tagIds);

        return response()->json(['data' => $this->projectPayload($project->load(['category', 'tags', 'uploader']), true)], 201);
    }

    public function update(UpdateProjectRequest $request, Project $project, ProjectFileManager $files, ProjectTagResolver $tags)
    {
        $validated = $request->validated();
        $tagIds = $tags->resolve($validated['tags'] ?? [], $validated['new_tags'] ?? null);
        unset($validated['tags'], $validated['new_tags']);

        $payload = $this->projectPayloadFromRequest($validated);

        if ($request->hasFile('pdf_file')) {
            $files->delete($project);
            $payload = array_merge($payload, $files->store($request->file('pdf_file'), $project));
        }

        $project->update($payload);
        $project->tags()->sync($tagIds);

        return response()->json(['data' => $this->projectPayload($project->load(['category', 'tags', 'uploader']), true)]);
    }

    public function destroy(Request $request, Project $project)
    {
        abort_unless($request->user()->canDeleteProjects(), 403);

        $project->delete();

        return response()->json(['message' => 'Project deleted.']);
    }

    public function categories()
    {
        return response()->json([
            'data' => Category::query()->withCount('projects')->orderBy('name')->get(),
        ]);
    }

    public function tags()
    {
        return response()->json([
            'data' => Tag::query()->withCount('projects')->orderBy('name')->get(),
        ]);
    }

    private function projectPayload(Project $project, bool $includeBody = false): array
    {
        $payload = [
            'id' => $project->id,
            'title' => $project->title,
            'author' => $project->student_name,
            'supervisor' => $project->supervisor,
            'project_type' => $project->project_type,
            'completion_year' => $project->completion_year,
            'category' => $project->category,
            'tags' => $project->tags,
            'keywords' => $project->keywords,
            'preview_url' => route('projects.preview', $project),
            'download_url' => route('projects.download', $project),
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
        ];

        if ($includeBody) {
            $payload['abstract'] = $project->abstract;
            $payload['uploaded_by'] = $project->uploader?->only(['id', 'name', 'email', 'role']);
        }

        return $payload;
    }

    private function projectPayloadFromRequest(array $validated): array
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
