<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::query()->withCount('projects')->orderBy('name')->paginate(30);

        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        Tag::create($this->validated($request));

        return redirect()->route('admin.tags.index')->with('success', 'Tag created.');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update($this->validated($request, $tag));

        return redirect()->route('admin.tags.index')->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'Tag deleted.');
    }

    private function validated(Request $request, ?Tag $tag = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('tags')->ignore($tag)],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        return $validated;
    }
}
