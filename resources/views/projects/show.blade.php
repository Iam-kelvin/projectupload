@extends('layouts.app')

@section('title', $project->title)

@section('content')
    @php
        $user = auth()->user();
        $canReadFullProject = auth()->check();
        $canEditProject = $project->canBeEditedBy($user);
        $hasPdf = $project->hasPdf();
        $textPreview = $project->pdfTextPreview($canReadFullProject ? 1800 : 520);
        $returnToProject = ['redirect' => url()->current()];
    @endphp

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

            @if ($project->abstract && $canReadFullProject)
                <section>
                    <h2>Abstract</h2>
                    <p class="body-copy">{{ $project->abstract }}</p>
                </section>
            @elseif (! $canReadFullProject && $textPreview)
                <section class="locked-panel">
                    <p class="eyebrow">Preview</p>
                    <p class="body-copy">{{ $textPreview }}</p>
                    @guest
                        <div class="hero-actions">
                            <a class="button" href="{{ route('login', $returnToProject) }}">Log in to continue</a>
                            <a class="button button-secondary" href="{{ route('register', $returnToProject) }}">Create account</a>
                        </div>
                    @endguest
                </section>
            @endif

            @if ($project->keywords && $canReadFullProject)
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
            @auth
                @if ($hasPdf)
                <a class="button" href="{{ route('projects.download', $project) }}">Download PDF</a>
                @endif
                @if ($saved)
                    <form action="{{ route('projects.unsave', $project) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="button button-ghost" type="submit">Remove saved</button>
                    </form>
                @else
                    <form action="{{ route('projects.save', $project) }}" method="POST">
                        @csrf
                        <button class="button button-secondary" type="submit">Save for later</button>
                    </form>
                @endif
                @if ($canEditProject)
                    <a class="button button-secondary" href="{{ route('projects.edit', $project) }}">Edit project</a>
                @endif
            @else
                <a class="button" href="{{ route('login', $returnToProject) }}">Log in to download</a>
                <a class="button button-secondary" href="{{ route('register', $returnToProject) }}">Create account</a>
            @endauth
        </aside>
    </div>

    <section class="pdf-panel">
        <div class="section-heading">
            <h2>PDF text preview</h2>
            @auth
                @if ($hasPdf)
                    <a href="{{ route('projects.preview', $project) }}" target="_blank" rel="noopener">Open PDF in new tab</a>
                @endif
            @else
                <a href="{{ route('login', $returnToProject) }}">Log in to open PDF</a>
            @endauth
        </div>

        @if ($textPreview)
            <div class="pdf-text-preview">
                <p class="body-copy">{{ $textPreview }}</p>
                @guest
                    <div class="hero-actions">
                        <a class="button" href="{{ route('login', $returnToProject) }}">Log in to continue reading</a>
                        <a class="button button-secondary" href="{{ route('register', $returnToProject) }}">Create account</a>
                    </div>
                @endguest
            </div>
        @else
            <div class="empty-state">
                No readable text preview is available yet. The PDF may be scanned, image-based, or waiting for text extraction.
            </div>
        @endif
    </section>
@endsection
