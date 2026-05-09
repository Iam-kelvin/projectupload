@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Users</h1>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Uploads</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roleLabel() }}</td>
                                <td>{{ $user->projects_count }}</td>
                                <td><a href="{{ route('admin.users.edit', $user) }}">Edit</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </section>
    </div>
@endsection
