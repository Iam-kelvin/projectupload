@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Upload Project</h2>
        <form action="{{ url('/projects') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="student_name">Student Name:</label>
                <input type="text" name="student_name" id="student_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="supervisor">Supervisor:</label>
                <input type="text" name="supervisor" id="supervisor" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="project_type">Project Type:</label>
                <input type="text" name="project_type" id="project_type" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="completion_year">Completion Year:</label>
                <input type="number" name="completion_year" id="completion_year" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="pdf_file">PDF File:</label>
                <input type="file" name="pdf_file" id="pdf_file" class="form-control-file" required accept=".pdf">
            </div>
            <button type="submit" class="btn btn-primary">Upload Project</button>
        </form>
    </div>
@endsection
