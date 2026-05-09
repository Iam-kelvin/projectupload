<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Project Library')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}">Project Library</a>

        <nav class="nav-links" aria-label="Primary">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('projects.index') }}">Browse</a>
            @auth
                @if (auth()->user()->canAccessAdminPanel())
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
            @endauth
        </nav>

        <div class="nav-actions">
            @auth
                <span class="role-pill">{{ auth()->user()->roleLabel() }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="link-button" type="submit">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a class="button button-small" href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </header>

    <main class="page-shell">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Please check the form.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
