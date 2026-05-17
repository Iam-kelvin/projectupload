@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <section class="auth-panel">
        <h1>Log in</h1>
        <p class="muted">Use your account to access staff tools and protected admin features.</p>

        <form method="POST" action="{{ route('login') }}" class="form-stack">
            @csrf
            @if (request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <label class="checkbox-line">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>
            <button class="button" type="submit">Log in</button>
        </form>
    </section>
@endsection
