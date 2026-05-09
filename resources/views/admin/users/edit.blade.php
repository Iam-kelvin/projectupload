@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">@include('admin.partials.nav')</aside>
        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Edit user</h1>
                </div>
            </div>

            <form class="panel form-stack" action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <label>Name
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </label>
                <label>Email
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>
                @if (! auth()->user()->is($user))
                    <label>Role
                        <select name="role" required>
                            @foreach ($roles as $role => $label)
                                <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                @else
                    <p class="muted">You cannot change your own role from here.</p>
                @endif
                <div class="form-actions">
                    <button class="button" type="submit">Save user</button>
                    <a class="button button-ghost" href="{{ route('admin.users.index') }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
