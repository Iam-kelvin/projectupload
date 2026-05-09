@extends('layouts.app')

@section('title', 'Upload Project')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Upload project</h1>
                </div>
            </div>

            <form class="panel form-grid" action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.projects.form', ['project' => null])
                <div class="form-actions">
                    <button class="button" type="submit">Upload project</button>
                    <a class="button button-ghost" href="{{ route('admin.projects.index') }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
