@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="admin-shell">
        <aside class="admin-nav">
            @include('admin.partials.nav')
        </aside>

        <section class="admin-content">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin</p>
                    <h1>Dashboard</h1>
                </div>
                <a class="button" href="{{ route('admin.projects.create') }}">Upload project</a>
            </div>

            <div class="stats-grid">
                <div><span>{{ number_format($stats['projects']) }}</span><p>Projects</p></div>
                <div><span>{{ number_format($stats['categories']) }}</span><p>Categories</p></div>
                <div><span>{{ number_format($stats['tags']) }}</span><p>Tags</p></div>
                @if (auth()->user()->canManageUsers())
                    <div><span>{{ number_format($stats['users']) }}</span><p>Users</p></div>
                @endif
            </div>

            <div class="dashboard-grid">
                <section class="panel">
                    <h2>Uploads by year</h2>
                    @foreach ($projectsPerYear as $row)
                        <div class="metric-row"><span>{{ $row->completion_year }}</span><strong>{{ $row->total }}</strong></div>
                    @endforeach
                </section>
                <section class="panel">
                    <h2>Top supervisors</h2>
                    @foreach ($topSupervisors as $row)
                        <div class="metric-row"><span>{{ $row->supervisor }}</span><strong>{{ $row->total }}</strong></div>
                    @endforeach
                </section>
                <section class="panel">
                    <h2>Project types</h2>
                    @foreach ($projectTypes as $row)
                        <div class="metric-row"><span>{{ $row->project_type }}</span><strong>{{ $row->total }}</strong></div>
                    @endforeach
                </section>
            </div>

            <section class="panel">
                <div class="section-heading">
                    <h2>Recent projects</h2>
                    <a href="{{ route('admin.projects.index') }}">Manage all</a>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Uploaded by</th>
                                <th>Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentProjects as $project)
                                <tr>
                                    <td><a href="{{ route('admin.projects.edit', $project) }}">{{ $project->title }}</a></td>
                                    <td>{{ $project->category?->name ?? 'Uncategorized' }}</td>
                                    <td>{{ $project->uploader?->name ?? 'Unknown' }}</td>
                                    <td>{{ $project->completion_year }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </div>
@endsection
