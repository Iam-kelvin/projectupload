@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Categories</h1>
                </div>
            </div>

            <form class="panel form-inline" action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Category name" required>
                <input type="text" name="description" placeholder="Short description">
                <button class="button" type="submit">Add category</button>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Projects</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->projects_count }}</td>
                                <td>{{ $category->description }}</td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" data-confirm="Delete this category? Projects will become uncategorized.">
                                        @csrf
                                        @method('DELETE')
                                        <button class="link-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $categories->links() }}
        </section>
    </div>
@endsection
