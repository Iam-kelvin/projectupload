@extends('layouts.app')

@section('title', 'Project Library')

@section('content')
    <section class="hero">
        <div>
            <p class="eyebrow">Searchable PDF repository</p>
            <h1>Find, organize, and manage project documents without memorizing routes.</h1>
            <p class="lede">Browse uploaded projects, search across metadata and extracted PDF text, or manage the library from the admin panel if your role allows it.</p>
            <form class="hero-search" action="{{ route('projects.index') }}" method="GET">
                <input type="search" name="q" placeholder="Search title, author, supervisor, keyword, or PDF text">
                <button class="button" type="submit">Search</button>
            </form>
            <div class="hero-actions">
                <a class="button" href="{{ route('projects.index') }}">Browse projects</a>
                @auth
                    @if (auth()->user()->canAccessAdminPanel())
                        <a class="button button-secondary" href="{{ route('admin.projects.create') }}">Upload project</a>
                    @endif
                @else
                    <a class="button button-secondary" href="{{ route('login') }}">Staff login</a>
                @endauth
            </div>
        </div>
        <div class="stat-panel">
            <div>
                <span>{{ number_format($stats['projects']) }}</span>
                <p>Total projects</p>
            </div>
            <div>
                <span>{{ number_format($stats['categories']) }}</span>
                <p>Categories</p>
            </div>
            <div>
                <span>{{ number_format($stats['years']) }}</span>
                <p>Years covered</p>
            </div>
        </div>
    </section>

    <section class="section-grid">
        <div>
            <div class="section-heading">
                <h2>Latest projects</h2>
                <a href="{{ route('projects.index') }}">View all</a>
            </div>
            <div class="project-grid">
                @forelse ($latestProjects as $project)
                    <article class="project-card">
                        <p class="muted">{{ $project->category?->name ?? 'Uncategorized' }}</p>
                        <h3><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></h3>
                        <p>{{ $project->student_name }} · {{ $project->completion_year }}</p>
                    </article>
                @empty
                    <p class="empty-state">No projects have been added yet.</p>
                @endforelse
            </div>
        </div>

        <aside class="side-panel">
            <h2>Popular project types</h2>
            @forelse ($popularTypes as $type)
                <a class="pill-row" href="{{ route('projects.index', ['project_type' => $type->project_type]) }}">
                    <span>{{ $type->project_type }}</span>
                    <strong>{{ $type->total }}</strong>
                </a>
            @empty
                <p class="muted">Project type stats will appear after uploads.</p>
            @endforelse
        </aside>
    </section>
@endsection
