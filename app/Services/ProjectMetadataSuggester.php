<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;

class ProjectMetadataSuggester
{
    private const STOP_WORDS = [
        'about',
        'after',
        'also',
        'analysis',
        'based',
        'between',
        'chapter',
        'design',
        'from',
        'have',
        'into',
        'paper',
        'project',
        'research',
        'study',
        'that',
        'their',
        'this',
        'through',
        'using',
        'with',
    ];

    private const CATEGORY_HINTS = [
        'arts' => ['art', 'culture', 'history', 'language', 'literature', 'music', 'philosophy', 'religion', 'theatre'],
        'business' => ['accounting', 'banking', 'brand', 'business', 'commerce', 'economics', 'finance', 'management', 'marketing'],
        'data' => ['analytics', 'algorithm', 'data', 'dataset', 'forecast', 'machine learning', 'model', 'statistics'],
        'education' => ['education', 'learning', 'pedagogy', 'school', 'social', 'student', 'teacher'],
        'engineering' => ['building', 'civil', 'construction', 'design', 'electrical', 'energy', 'engineering', 'manufacturing', 'mechanical'],
        'environment' => ['agriculture', 'climate', 'ecology', 'environment', 'renewable', 'sustainability', 'waste', 'water'],
        'health' => ['biology', 'care', 'clinical', 'disease', 'health', 'hospital', 'medicine', 'nursing', 'patient'],
        'law' => ['governance', 'justice', 'law', 'legal', 'policy', 'political', 'regulation', 'rights'],
        'technology' => ['application', 'cloud', 'computer', 'cybersecurity', 'information system', 'mobile', 'software', 'technology', 'web'],
    ];

    public function suggest(array $payload, ?string $pdfText, array $currentTagIds = []): array
    {
        $text = $this->cleanText(implode(' ', array_filter([
            $payload['title'] ?? null,
            $payload['student_name'] ?? null,
            $payload['supervisor'] ?? null,
            $payload['project_type'] ?? null,
            $payload['abstract'] ?? null,
            $payload['keywords'] ?? null,
            $pdfText,
        ])));

        if ($text === '') {
            return ['payload' => [], 'tag_ids' => []];
        }

        $updates = [];
        $tagIds = [];

        if (blank($payload['abstract'] ?? null)) {
            $updates['abstract'] = $this->makeAbstract($pdfText ?: $text);
        }

        if (blank($payload['category_id'] ?? null)) {
            $updates['category_id'] = $this->bestCategoryId($text);
        }

        if (empty($currentTagIds)) {
            $tagIds = $this->bestTagIds($text);
        }

        if (blank($payload['keywords'] ?? null)) {
            $keywords = $this->keywordsFrom($text);

            if ($keywords !== []) {
                $updates['keywords'] = implode(', ', $keywords);
            }
        }

        return [
            'payload' => array_filter($updates, fn ($value) => ! blank($value)),
            'tag_ids' => $tagIds,
        ];
    }

    private function bestCategoryId(string $text): ?int
    {
        return Category::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => [
                'category' => $category,
                'score' => $this->score($category->name, $text, $this->categoryHints($category)),
            ])
            ->filter(fn (array $item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->first()['category']->id ?? null;
    }

    private function bestTagIds(string $text): array
    {
        $matched = Tag::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Tag $tag) => [
                'tag' => $tag,
                'score' => $this->score($tag->name, $text),
            ])
            ->filter(fn (array $item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take(5)
            ->map(fn (array $item) => $item['tag']->id)
            ->values()
            ->all();

        if ($matched !== []) {
            return $matched;
        }

        return collect($this->keywordsFrom($text))
            ->take(3)
            ->map(function (string $keyword) {
                $name = Str::headline($keyword);

                return Tag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                )->id;
            })
            ->values()
            ->all();
    }

    private function score(string $name, string $text, array $extraHints = []): int
    {
        $score = 0;
        $needles = collect(array_merge($this->termsFrom($name), $extraHints))
            ->map(fn ($term) => $this->cleanText($term))
            ->filter(fn ($term) => mb_strlen($term) >= 3)
            ->unique();

        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                $score += str_contains($needle, ' ') ? 5 : 2;
            }
        }

        return $score;
    }

    private function categoryHints(Category $category): array
    {
        $name = Str::lower($category->name);
        $slug = Str::lower($category->slug);

        foreach (self::CATEGORY_HINTS as $key => $hints) {
            if (str_contains($name, $key) || str_contains($slug, $key)) {
                return $hints;
            }
        }

        return [];
    }

    private function makeAbstract(string $text): string
    {
        $text = $this->cleanText($text);

        return Str::of($text)->limit(850)->toString();
    }

    private function keywordsFrom(string $text): array
    {
        return collect($this->termsFrom($text))
            ->reject(fn ($term) => in_array($term, self::STOP_WORDS, true))
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(6)
            ->values()
            ->all();
    }

    private function termsFrom(string $text): array
    {
        return collect(preg_split('/[^a-z0-9]+/i', $text) ?: [])
            ->map(fn ($term) => Str::lower(trim($term)))
            ->filter(fn ($term) => mb_strlen($term) >= 4)
            ->values()
            ->all();
    }

    private function cleanText(?string $text): string
    {
        return Str::of((string) $text)->squish()->lower()->toString();
    }
}
