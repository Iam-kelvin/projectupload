<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProjectRecommendationService
{
    public function forUser(User $user, int $limit = 8): Collection
    {
        $categoryIds = $user->preferredCategoryIds();
        $terms = Project::searchTerms(implode(' ', $user->interestTerms()));

        $query = Project::query()->with(['category', 'tags']);

        if ($categoryIds || $terms) {
            $query->where(function (Builder $query) use ($categoryIds, $terms) {
                if ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                }

                foreach ($terms as $term) {
                    $like = "%{$term}%";

                    $query->orWhere('title', 'like', $like)
                        ->orWhere('student_name', 'like', $like)
                        ->orWhere('supervisor', 'like', $like)
                        ->orWhere('project_type', 'like', $like)
                        ->orWhere('abstract', 'like', $like)
                        ->orWhere('keywords', 'like', $like)
                        ->orWhere('pdf_text', 'like', $like)
                        ->orWhereHas('category', fn ($query) => $query->where('name', 'like', $like))
                        ->orWhereHas('tags', fn ($query) => $query->where('name', 'like', $like));
                }
            });

            $this->applyPreferenceOrder($query, $categoryIds, $terms);
        }

        return $query->latest()->limit($limit)->get();
    }

    private function applyPreferenceOrder(Builder $query, array $categoryIds, array $terms): void
    {
        if ($categoryIds) {
            $placeholders = implode(', ', array_fill(0, count($categoryIds), '?'));
            $query->orderByRaw("CASE WHEN category_id IN ({$placeholders}) THEN 0 ELSE 1 END", $categoryIds);
        }

        if (! $terms) {
            return;
        }

        $scoreParts = [];
        $bindings = [];

        foreach ($terms as $term) {
            $like = "%{$term}%";
            $scoreParts[] = 'CASE WHEN title LIKE ? THEN 20 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN keywords LIKE ? THEN 14 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN abstract LIKE ? THEN 8 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN pdf_text LIKE ? THEN 4 ELSE 0 END';
            array_push($bindings, $like, $like, $like, $like);
        }

        $query->orderByRaw('('.implode(' + ', $scoreParts).') DESC', $bindings);
    }
}
