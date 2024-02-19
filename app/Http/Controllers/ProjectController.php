<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function create()
    {
        return view('upload');
    }

    public function index(Request $request)
    {
        $projects = Project::all();

        $search = $request->input('search');
        $projects = Project::query()
            ->where('title', 'LIKE', "%{$search}%")
            ->orWhere('student_name', 'LIKE', "%{$search}%")
            ->orWhere('supervisor', 'LIKE', "%{$search}%")
            ->orWhere('project_type', 'LIKE', "%{$search}%")
            ->orWhere('completion_year', 'LIKE', "%{$search}%")
            ->get();
            

        return view('index', compact('projects'));
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return view('show', compact('project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required',
            'supervisor' => 'required',
            'title' => 'required',
            'project_type' => 'required',
            'completion_year' => 'required|integer',
            'pdf_file' => 'required|mimes:pdf|max:2048', // Assuming maximum file size is 2MB
        ]);

        $fileName = time().'.'.$request->pdf_file->extension();  
        $request->pdf_file->move(public_path('uploads'), $fileName);

        Project::create([
            'student_name' => $request->student_name,
            'supervisor' => $request->supervisor,
            'title' => $request->title,
            'project_type' => $request->project_type,
            'completion_year' => $request->completion_year,
            'pdf_file' => $fileName,
        ]);

        return redirect('/')->with('success', 'Project uploaded successfully.');
    }
}
