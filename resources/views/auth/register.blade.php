@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <section class="auth-panel">
        <h1>Create account</h1>
        <p class="muted">The first account becomes Super Admin. After that, new accounts start as normal users until elevated by a privileged admin.</p>

        <form method="POST" action="{{ route('register') }}" class="form-stack">
            @csrf
            <label>Name
                <input type="text" name="name" value="{{ old('name') }}" required autofocus>
            </label>
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <label>Confirm password
                <input type="password" name="password_confirmation" required>
            </label>
            <button class="button" type="submit">Create account</button>
        </form>
    </section>
@endsection
