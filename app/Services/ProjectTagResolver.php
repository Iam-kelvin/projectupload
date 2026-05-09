<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

class ProjectTagResolver
{
    public function resolve(array $selectedTagIds = [], ?string $newTags = null): array
    {
        $tagIds = collect($selectedTagIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($this->parseNewTags($newTags) as $name) {
            $tag = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );

            $tagIds[] = $tag->id;
        }

        return collect($tagIds)->unique()->values()->all();
    }

    private function parseNewTags(?string $newTags): array
    {
        if (! $newTags) {
            return [];
        }

        return collect(preg_split('/[,;\n]+/', $newTags) ?: [])
            ->map(fn ($tag) => trim(preg_replace('/\s+/', ' ', $tag)))
            ->filter()
            ->map(fn ($tag) => Str::of($tag)->limit(80, '')->trim()->toString())
            ->unique(fn ($tag) => Str::slug($tag))
            ->values()
            ->all();
    }
}
