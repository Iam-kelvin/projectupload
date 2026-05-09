@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Edit category</h1>
                </div>
            </div>

            <form class="panel form-stack" action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <label>Name
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
                </label>
                <label>Description
                    <textarea name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                </label>
                <div class="form-actions">
                    <button class="button" type="submit">Save category</button>
                    <a class="button button-ghost" href="{{ route('admin.categories.index') }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
