<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class SavedProjectController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->user()->savedProjects()->syncWithoutDetaching([$project->id]);

        return back()->with('success', 'Project saved for later.');
    }

    public function destroy(Request $request, Project $project)
    {
        $request->user()->savedProjects()->detach($project->id);

        return back()->with('success', 'Project removed from saved projects.');
    }
}
