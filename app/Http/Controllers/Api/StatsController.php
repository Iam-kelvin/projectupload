<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'data' => [
                'total_projects' => Project::query()->count(),
                'projects_per_year' => Project::query()
                    ->select('completion_year', DB::raw('COUNT(*) as total'))
                    ->whereNotNull('completion_year')
                    ->groupBy('completion_year')
                    ->orderByDesc('completion_year')
                    ->get(),
                'top_supervisors' => Project::query()
                    ->select('supervisor', DB::raw('COUNT(*) as total'))
                    ->whereNotNull('supervisor')
                    ->groupBy('supervisor')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get(),
                'project_types' => Project::query()
                    ->select('project_type', DB::raw('COUNT(*) as total'))
                    ->whereNotNull('project_type')
                    ->groupBy('project_type')
                    ->orderByDesc('total')
                    ->get(),
            ],
        ]);
    }
}
