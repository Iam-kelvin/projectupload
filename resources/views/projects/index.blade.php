@extends('layouts.app')

@section('title', 'Browse Projects')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Repository</p>
            <h1>Browse projects</h1>
        </div>
        @auth
            @if (auth()->user()->canAccessAdminPanel())
                <a class="button" href="{{ route('admin.projects.create') }}">Upload project</a>
            @endif
        @endauth
    </div>

    <form class="filter-panel" method="GET" action="{{ route('projects.index') }}">
        <div class="filter-grid">
            <label class="filter-search">Search
                <input type="search" name="q" value="{{ request('q', request('search')) }}" placeholder="Title, author, keyword, PDF text">
            </label>
            <label>Category
                <select name="category_id">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Tag
                <select name="tag_id">
                    <option value="">All tags</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected((string) request('tag_id') === (string) $tag->id)>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Project type
                <select name="project_type">
                    <option value="">All types</option>
                    @foreach ($projectTypes as $type)
                        <option value="{{ $type }}" @selected(request('project_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </label>
            <label>From year
                <input type="number" name="year_from" value="{{ request('year_from') }}" min="1900">
            </label>
            <label>To year
                <input type="number" name="year_to" value="{{ request('year_to') }}" min="1900">
            </label>
            <label>Sort
                <select name="sort">
                    <option value="">Newest</option>
                    <option value="relevance" @selected(request('sort') === 'relevance')>Most relevant</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                    <option value="title" @selected(request('sort') === 'title')>Title</option>
                    <option value="year_desc" @selected(request('sort') === 'year_desc')>Year, high to low</option>
                    <option value="year_asc" @selected(request('sort') === 'year_asc')>Year, low to high</option>
                </select>
            </label>
            <div class="filter-actions">
                <button class="button" type="submit">Apply filters</button>
                <a class="button button-ghost" href="{{ route('projects.index') }}">Reset</a>
            </div>
        </div>
    </form>

    <div class="results-bar">
        <span>{{ number_format($projects->total()) }} project{{ $projects->total() === 1 ? '' : 's' }} found</span>
        @if (request('q', request('search')))
            <span>Ranked by match strength</span>
        @endif
    </div>

    <div class="project-list">
        @forelse ($projects as $project)
            <article class="project-row">
                <div>
                    <p class="muted">{{ $project->category?->name ?? 'Uncategorized' }} &middot; {{ $project->completion_year }}</p>
                    <h2><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></h2>
                    <p>{{ $project->student_name }} @if ($project->supervisor) &middot; {{ $project->supervisor }} @endif</p>
                    @if ($project->abstract)
                        <p class="summary">{{ str($project->abstract)->limit(180) }}</p>
                    @endif
                    <div class="tag-list">
                        @foreach ($project->tags as $tag)
                            <a href="{{ route('projects.index', ['tag_id' => $tag->id]) }}">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="row-actions">
                    <a class="button button-small" href="{{ route('projects.show', $project) }}">View</a>
                    @if ($project->pdfAbsolutePath())
                        <a class="button button-small button-secondary" href="{{ route('projects.download', $project) }}">Download</a>
                    @endif
                </div>
            </article>
        @empty
            <p class="empty-state">No projects match the current filters.</p>
        @endforelse
    </div>

    {{ $projects->links() }}
@endsection
