@extends('layouts.app')

@section('title', 'Upload Project')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Repository</p>
            <h1>Upload project</h1>
        </div>
        <a class="button button-secondary" href="{{ route('dashboard') }}">My dashboard</a>
    </div>

    <form class="panel form-grid" action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.projects.form', ['project' => null])
        <div class="form-actions">
            <button class="button" type="submit">Upload project</button>
            <a class="button button-ghost" href="{{ route('dashboard') }}">Cancel</a>
        </div>
    </form>
@endsection
