<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q', $request->query('search'));
        $sort = $request->query('sort') ?: (trim((string) $search) !== '' ? 'relevance' : null);

        $query = Project::query()
            ->with(['category', 'tags'])
            ->filtered($request->query());

        if (trim((string) $search) !== '' && $sort === 'relevance') {
            $query->rankedForSearch($search);
        }

        $projects = $query->sorted($sort)
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();
        $tags = Tag::query()->orderBy('name')->get();
        $projectTypes = Project::query()
            ->whereNotNull('project_type')
            ->distinct()
            ->orderBy('project_type')
            ->pluck('project_type');

        return view('projects.index', compact('projects', 'categories', 'tags', 'projectTypes'));
    }

    public function show(Request $request, Project $project)
    {
        $project->load(['category', 'tags', 'uploader']);
        $saved = false;

        if ($request->user()) {
            $request->user()->viewedProjects()->syncWithoutDetaching([$project->id]);
            $request->user()->viewedProjects()->updateExistingPivot($project->id, ['updated_at' => now()]);
            $saved = $request->user()->savedProjects()->whereKey($project->id)->exists();
        }

        return view('projects.show', compact('project', 'saved'));
    }

    public function preview(Project $project)
    {
        if ($project->pdfSourceUrl()) {
            return redirect()->away($project->pdfSourceUrl());
        }

        $path = $project->pdfAbsolutePath();

        abort_if(! $path, 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$project->pdfDisplayName().'"',
        ]);
    }

    public function download(Project $project)
    {
        if ($project->pdfSourceUrl()) {
            return redirect()->away($project->pdfSourceUrl());
        }

        $path = $project->pdfAbsolutePath();

        abort_if(! $path, 404);

        return response()->download($path, $project->pdfDisplayName(), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function legacyShow(Project $project)
    {
        return redirect()->route('projects.show', $project, Response::HTTP_MOVED_PERMANENTLY);
    }
}
