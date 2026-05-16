@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <section class="feed-hero">
        <div>
            <p class="eyebrow">Research desk</p>
            <h1>Welcome back, {{ str($user->name)->before(' ') }}.</h1>
            <p class="lede">Start from projects that match your interests, scan new uploads, or search the full library by topic, author, supervisor, category, tag, abstract, and extracted PDF text.</p>

            <form class="search-strip" action="{{ route('projects.index') }}" method="GET">
                <input type="search" name="q" placeholder="Search projects, methods, keywords, or PDF text">
                <button class="button" type="submit">Search</button>
            </form>
        </div>

        <aside class="stat-panel" aria-label="Repository summary">
            <div>
                <span>{{ number_format($stats['projects']) }}</span>
                <p>Projects</p>
            </div>
            <div>
                <span>{{ number_format($stats['categories']) }}</span>
                <p>Fields</p>
            </div>
            <div>
                <span>{{ number_format($stats['years']) }}</span>
                <p>Years covered</p>
            </div>
        </aside>
    </section>

    <section class="feed-layout">
        <div class="feed-main">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">For you</p>
                    <h2>Recommended projects</h2>
                </div>
                <a href="{{ route('profile.preferences.edit') }}">Tune interests</a>
            </div>

            <div class="project-grid">
                @forelse ($recommendedProjects as $project)
                    <article class="project-card">
                        <p class="muted">{{ $project->category?->name ?? 'Uncategorized' }} &middot; {{ $project->completion_year }}</p>
                        <h3><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></h3>
                        <p>{{ $project->student_name }} @if ($project->supervisor) &middot; {{ $project->supervisor }} @endif</p>
                        @if ($project->abstract)
                            <p class="summary">{{ str($project->abstract)->limit(140) }}</p>
                        @endif
                    </article>
                @empty
                    <p class="empty-state">Add interests to your profile or upload more projects to unlock better recommendations.</p>
                @endforelse
            </div>

            <div class="section-heading section-heading-spaced">
                <div>
                    <p class="eyebrow">Fresh uploads</p>
                    <h2>New in the library</h2>
                </div>
                <a href="{{ route('projects.index') }}">Browse all</a>
            </div>

            <div class="project-list">
                @forelse ($latestProjects as $project)
                    <article class="project-row compact-row">
                        <div>
                            <p class="muted">{{ $project->category?->name ?? 'Uncategorized' }} &middot; {{ $project->completion_year }}</p>
                            <h2><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></h2>
                            <p>{{ $project->student_name }} @if ($project->supervisor) &middot; {{ $project->supervisor }} @endif</p>
                        </div>
                        <div class="row-actions">
                            <a class="button button-small" href="{{ route('projects.show', $project) }}">View</a>
                        </div>
                    </article>
                @empty
                    <p class="empty-state">No projects have been added yet.</p>
                @endforelse
            </div>
        </div>

        <aside class="feed-side">
            <section class="panel">
                <p class="eyebrow">Your profile</p>
                <h2>Research signal</h2>
                @if ($user->hasResearchPreferences())
                    @if ($user->field_of_study)
                        <p><strong>Field:</strong> {{ $user->field_of_study }}</p>
                    @endif
                    @if ($preferredCategories->isNotEmpty())
                        <div class="tag-list">
                            @foreach ($preferredCategories as $category)
                                <a href="{{ route('projects.index', ['category_id' => $category->id]) }}">{{ $category->name }}</a>
                            @endforeach
                        </div>
                    @endif
                    @if ($user->interest_keywords)
                        <p class="summary">{{ $user->interest_keywords }}</p>
                    @endif
                @else
                    <p class="muted">Tell the library what you care about so the first screen feels less generic.</p>
                @endif
                <a class="button button-secondary button-full" href="{{ route('profile.preferences.edit') }}">Edit interests</a>
            </section>

            <section class="panel">
                <p class="eyebrow">Explore</p>
                <h2>Active fields</h2>
                <div class="metric-list">
                    @forelse ($categories as $category)
                        <a class="metric-row" href="{{ route('projects.index', ['category_id' => $category->id]) }}">
                            <span>{{ $category->name }}</span>
                            <strong>{{ $category->projects_count }}</strong>
                        </a>
                    @empty
                        <p class="muted">Categories will appear after seeding.</p>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <p class="eyebrow">Topics</p>
                <h2>Popular tags</h2>
                <div class="tag-list">
                    @forelse ($popularTags as $tag)
                        <a href="{{ route('projects.index', ['tag_id' => $tag->id]) }}">{{ $tag->name }}</a>
                    @empty
                        <p class="muted">Tags will appear after uploads.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </section>
@endsection
