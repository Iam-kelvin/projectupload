<nav class="admin-menu" aria-label="Admin">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <a href="{{ route('admin.projects.index') }}">Projects</a>
    @if (auth()->user()->canManageTaxonomy())
        <a href="{{ route('admin.categories.index') }}">Categories</a>
        <a href="{{ route('admin.tags.index') }}">Tags</a>
    @endif
    @if (auth()->user()->canManageUsers())
        <a href="{{ route('admin.users.index') }}">Users</a>
    @endif
</nav>
