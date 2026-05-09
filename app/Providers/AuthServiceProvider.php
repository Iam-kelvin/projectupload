<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('access-admin', fn (User $user) => $user->canAccessAdminPanel());
        Gate::define('manage-taxonomy', fn (User $user) => $user->canManageTaxonomy());
        Gate::define('delete-projects', fn (User $user) => $user->canDeleteProjects());
        Gate::define('manage-users', fn (User $user) => $user->canManageUsers());
    }
}
