<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()->withCount('projects')->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function edit(Request $request, User $user)
    {
        $this->authorizeVisibleUser($request, $user);
        $roles = $request->user()->manageableRoles();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeVisibleUser($request, $user);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
        ];

        if (! $request->user()->is($user)) {
            $rules['role'] = ['required', Rule::in(array_keys($request->user()->manageableRoles()))];
        }

        $validated = $request->validate($rules);

        if ($request->user()->is($user)) {
            unset($validated['role']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    private function authorizeVisibleUser(Request $request, User $user): void
    {
        abort_unless($request->user()->canManageUsers(), 403);
    }
}
