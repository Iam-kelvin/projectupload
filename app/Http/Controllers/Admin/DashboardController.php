<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'projects' => Project::query()->count(),
            'categories' => Category::query()->count(),
            'tags' => Tag::query()->count(),
            'users' => User::query()->count(),
        ];

        $projectsPerYear = Project::query()
            ->select('completion_year', DB::raw('COUNT(*) as total'))
            ->whereNotNull('completion_year')
            ->groupBy('completion_year')
            ->orderByDesc('completion_year')
            ->limit(8)
            ->get();

        $topSupervisors = Project::query()
            ->select('supervisor', DB::raw('COUNT(*) as total'))
            ->whereNotNull('supervisor')
            ->groupBy('supervisor')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $projectTypes = Project::query()
            ->select('project_type', DB::raw('COUNT(*) as total'))
            ->whereNotNull('project_type')
            ->groupBy('project_type')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $recentProjects = Project::query()
            ->with(['category', 'uploader'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'projectsPerYear',
            'topSupervisors',
            'projectTypes',
            'recentProjects'
        ));
    }
}
