<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('auth.register', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'field_of_study' => ['nullable', 'string', 'max:120'],
            'interest_keywords' => ['nullable', 'string', 'max:600'],
            'preferred_categories' => ['nullable', 'array'],
            'preferred_categories.*' => ['integer', 'exists:categories,id'],
        ]);

        $role = User::query()->exists() ? User::ROLE_USER : User::ROLE_SUPER_ADMIN;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'field_of_study' => $validated['field_of_study'] ?? null,
            'interest_keywords' => $validated['interest_keywords'] ?? null,
            'preferred_categories' => $validated['preferred_categories'] ?? [],
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
