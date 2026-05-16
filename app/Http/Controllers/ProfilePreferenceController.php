<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class ProfilePreferenceController extends Controller
{
    public function edit(Request $request)
    {
        $categories = Category::query()->orderBy('name')->get();
        $user = $request->user();

        return view('profile.preferences', compact('categories', 'user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'field_of_study' => ['nullable', 'string', 'max:120'],
            'interest_keywords' => ['nullable', 'string', 'max:600'],
            'preferred_categories' => ['nullable', 'array'],
            'preferred_categories.*' => ['integer', 'exists:categories,id'],
        ]);

        $request->user()->update([
            'field_of_study' => $validated['field_of_study'] ?? null,
            'interest_keywords' => $validated['interest_keywords'] ?? null,
            'preferred_categories' => $validated['preferred_categories'] ?? [],
        ]);

        return redirect()->route('dashboard')->with('success', 'Your research interests were updated.');
    }
}
