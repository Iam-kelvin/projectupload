@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Tags</h1>
                </div>
            </div>

            <form class="panel form-inline" action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Tag name" required>
                <button class="button" type="submit">Add tag</button>
            </form>

            <div class="tag-admin-grid">
                @foreach ($tags as $tag)
                    <div class="tag-admin-item">
                        <span>{{ $tag->name }}</span>
                        <small>{{ $tag->projects_count }} projects</small>
                        <div class="table-actions">
                            <a href="{{ route('admin.tags.edit', $tag) }}">Edit</a>
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" data-confirm="Delete this tag?">
                                @csrf
                                @method('DELETE')
                                <button class="link-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{ $tags->links() }}
        </section>
    </div>
@endsection
