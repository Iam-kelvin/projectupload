@extends('layouts.app')

@section('title', 'Edit Tag')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Edit tag</h1>
                </div>
            </div>

            <form class="panel form-stack" action="{{ route('admin.tags.update', $tag) }}" method="POST">
                @csrf
                @method('PUT')
                <label>Name
                    <input type="text" name="name" value="{{ old('name', $tag->name) }}" required>
                </label>
                <div class="form-actions">
                    <button class="button" type="submit">Save tag</button>
                    <a class="button button-ghost" href="{{ route('admin.tags.index') }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
