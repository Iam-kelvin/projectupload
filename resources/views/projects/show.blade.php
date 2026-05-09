@extends('layouts.app')

@section('title', $project->title)

@section('content')
    <div class="detail-layout">
        <article class="detail-main">
            <p class="eyebrow">{{ $project->category?->name ?? 'Uncategorized' }}</p>
            <h1>{{ $project->title }}</h1>

            <dl class="metadata-grid">
                <div>
                    <dt>Author</dt>
                    <dd>{{ $project->student_name }}</dd>
                </div>
                <div>
                    <dt>Supervisor</dt>
                    <dd>{{ $project->supervisor ?: 'Not listed' }}</dd>
                </div>
                <div>
                    <dt>Project type</dt>
                    <dd>{{ $project->project_type ?: 'Not listed' }}</dd>
                </div>
                <div>
                    <dt>Completion year</dt>
                    <dd>{{ $project->completion_year }}</dd>
                </div>
            </dl>

            @if ($project->abstract)
                <section>
                    <h2>Abstract</h2>
                    <p class="body-copy">{{ $project->abstract }}</p>
                </section>
            @endif

            @if ($project->keywords)
                <section>
                    <h2>Keywords</h2>
                    <p>{{ $project->keywords }}</p>
                </section>
            @endif

            @if ($project->tags->isNotEmpty())
                <div class="tag-list">
                    @foreach ($project->tags as $tag)
                        <a href="{{ route('projects.index', ['tag_id' => $tag->id]) }}">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif
        </article>

        <aside class="detail-side">
            @if ($project->pdfAbsolutePath())
                <a class="button" href="{{ route('projects.download', $project) }}">Download PDF</a>
            @endif
            @auth
                @if (auth()->user()->canAccessAdminPanel())
                    <a class="button button-secondary" href="{{ route('admin.projects.edit', $project) }}">Edit project</a>
                @endif
            @endauth
        </aside>
    </div>

    @if ($project->pdfAbsolutePath())
        <section class="pdf-panel">
            <div class="section-heading">
                <h2>PDF preview</h2>
                <a href="{{ route('projects.preview', $project) }}" target="_blank" rel="noopener">Open in new tab</a>
            </div>
            <iframe title="PDF preview for {{ $project->title }}" src="{{ route('projects.preview', $project) }}"></iframe>
        </section>
    @endif
@endsection
