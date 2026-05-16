<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use App\Services\ProjectRecommendationService;
use Illuminate\Http\Request;

class ResearchDashboardController extends Controller
{
    public function __invoke(Request $request, ProjectRecommendationService $recommendations)
    {
        $user = $request->user();
        $preferredCategoryIds = $user->preferredCategoryIds();

        $recommendedProjects = $recommendations->forUser($user, 6);
        $latestProjects = Project::query()
            ->with(['category', 'tags'])
            ->latest()
            ->limit(6)
            ->get();

        $categories = Category::query()
            ->withCount('projects')
            ->orderByDesc('projects_count')
            ->orderBy('name')
            ->limit(9)
            ->get();

        $preferredCategories = $preferredCategoryIds
            ? Category::query()->whereIn('id', $preferredCategoryIds)->orderBy('name')->get()
            : collect();

        $popularTags = Tag::query()
            ->withCount('projects')
            ->orderByDesc('projects_count')
            ->orderBy('name')
            ->limit(12)
            ->get();

        $stats = [
            'projects' => Project::query()->count(),
            'categories' => Category::query()->count(),
            'years' => Project::query()->whereNotNull('completion_year')->distinct()->count('completion_year'),
        ];

        return view('dashboard', compact(
            'categories',
            'latestProjects',
            'popularTags',
            'preferredCategories',
            'recommendedProjects',
            'stats',
            'user'
        ));
    }
}
