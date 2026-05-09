<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'projects' => Project::query()->count(),
            'categories' => Category::query()->count(),
            'years' => Project::query()->whereNotNull('completion_year')->distinct()->count('completion_year'),
        ];

        $latestProjects = Project::query()
            ->with(['category', 'tags'])
            ->latest()
            ->limit(6)
            ->get();

        $popularTypes = Project::query()
            ->select('project_type', DB::raw('COUNT(*) as total'))
            ->whereNotNull('project_type')
            ->groupBy('project_type')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('home', compact('stats', 'latestProjects', 'popularTypes'));
    }
}
