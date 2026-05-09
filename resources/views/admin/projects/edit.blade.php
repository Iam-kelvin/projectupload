@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Edit project</h1>
                </div>
                <a class="button button-secondary" href="{{ route('projects.show', $project) }}">View public page</a>
            </div>

            <form class="panel form-grid" action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.projects.form', ['project' => $project])
                <div class="form-actions">
                    <button class="button" type="submit">Save changes</button>
                    <a class="button button-ghost" href="{{ route('admin.projects.index') }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
