@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Heading for displaying all projects -->
        <h2>All Projects</h2>

        <!-- Search form for projects -->
        <form action="{{ url('/') }}" method="GET" class="mb-3">
            <div class="input-group">
                <!-- Input field for searching projects -->
                <input type="text" name="search" class="form-control" placeholder="Search projects">

                <!-- Button to submit the search form -->
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <!-- Display projects if available -->
        @if ($projects->count() > 0)
            <ul class="list-group">
                <!-- Loop through each project and display it -->
                @foreach ($projects as $project)
                    <li class="list-group-item">
                        <!-- Link to view the details of a project -->
                        <a href="{{ url('show/'.$project->id) }}">{{ $project->title }}</a> - {{ $project->student_name }} ({{ $project->completion_year }})
                        <!-- Link to download a PDF file -->
                        <a href="{{ asset('uploads/' . $project->pdf_file) }}" download>Download PDF</a>
                        {{-- <a href="{{ route('download.pdf', ['filename' => $pdf->fileName]) }}">Download PDF</a> --}}
                    </li>
                @endforeach
            </ul>
        @else
            <!-- Message when no projects are found -->
            <p>No projects found.</p>
        @endif
    </div>

    
@endsection
