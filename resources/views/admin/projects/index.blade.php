@extends('layouts.app')

@section('title', 'Manage Projects')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Manage projects</h1>
                </div>
                <a class="button" href="{{ route('admin.projects.create') }}">Upload project</a>
            </div>

            <form class="filter-panel compact" method="GET" action="{{ route('admin.projects.index') }}">
                <input type="search" name="q" value="{{ request('q', request('search')) }}" placeholder="Search projects">
                <select name="category_id">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="tag_id">
                    <option value="">All tags</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected((string) request('tag_id') === (string) $tag->id)>{{ $tag->name }}</option>
                    @endforeach
                </select>
                <select name="project_type">
                    <option value="">All types</option>
                    @foreach ($projectTypes as $type)
                        <option value="{{ $type }}" @selected(request('project_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
                <button class="button button-small" type="submit">Filter</button>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></td>
                                <td>{{ $project->student_name }}</td>
                                <td>{{ $project->category?->name ?? 'Uncategorized' }}</td>
                                <td>{{ $project->completion_year }}</td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.projects.edit', $project) }}">Edit</a>
                                    @if (auth()->user()->canDeleteProjects())
                                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" data-confirm="Delete this project?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="link-danger" type="submit">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No projects found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $projects->links() }}
        </section>
    </div>
@endsection
