<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Project Library')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @php
        $viteManifestPath = public_path('build/manifest.json');
        $viteScript = null;

        if (is_file($viteManifestPath)) {
            $viteManifest = json_decode(file_get_contents($viteManifestPath), true) ?: [];
            $viteScript = $viteManifest['resources/js/app.js']['file'] ?? null;
        }
    @endphp

    @if ($viteScript)
        <script type="module" src="{{ asset('build/'.$viteScript) }}"></script>
    @endif
</head>
<body>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}">Project Library</a>

        <nav class="nav-links" aria-label="Primary">
            <a href="{{ route('home') }}">Home</a>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
            @endauth
            <a href="{{ route('projects.index') }}">Browse</a>
            @auth
                <a href="{{ route('projects.create') }}">Upload</a>
            @endauth
            @auth
                @if (auth()->user()->canAccessAdminPanel())
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
            @endauth
        </nav>

        <div class="nav-actions">
            @auth
                <span class="role-pill">{{ auth()->user()->roleLabel() }}</span>
                <a href="{{ route('profile.preferences.edit') }}">Interests</a>
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
</body>
</html>
