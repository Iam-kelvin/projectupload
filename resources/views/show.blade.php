@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $project->title }}</h2>
        <p><strong>Student Name:</strong> {{ $project->student_name }}</p>
        <p><strong>Supervisor:</strong> {{ $project->supervisor }}</p>
        <p><strong>Project Type:</strong> {{ $project->project_type }}</p>
        <p><strong>Completion Year:</strong> {{ $project->completion_year }}</p>
        <a href="{{ asset('uploads/'.$project->pdf_file) }}" target="_blank" class="btn btn-primary">View PDF</a>
    </div>
@endsection
