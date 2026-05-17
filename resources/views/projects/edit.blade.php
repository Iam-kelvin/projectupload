@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Repository</p>
            <h1>Edit project</h1>
        </div>
        <a class="button button-secondary" href="{{ route('projects.show', $project) }}">View project</a>
    </div>

    <form class="panel form-grid" action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.projects.form', ['project' => $project])
        <div class="form-actions">
            <button class="button" type="submit">Save changes</button>
            <a class="button button-ghost" href="{{ route('projects.show', $project) }}">Cancel</a>
        </div>
    </form>
@endsection
