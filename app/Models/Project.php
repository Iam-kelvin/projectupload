<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'student_name',
        'supervisor',
        'title',
        'project_type',
        'abstract',
        'keywords',
        'completion_year',
        'pdf_file',
        'pdf_path',
        'pdf_storage_disk',
        'pdf_storage_key',
        'pdf_url',
        'pdf_download_url',
        'pdf_original_name',
        'pdf_mime',
        'pdf_size',
        'file_hash',
        'pdf_text',
        'uploaded_by',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'project_views')->withTimestamps();
    }

    public function savers()
    {
        return $this->belongsToMany(User::class, 'project_saves')->withTimestamps();
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->canAccessAdminPanel() || ((int) $this->uploaded_by === (int) $user->id);
    }

    public function guestSnippet(int $limit = 260): string
    {
        return $this->pdfTextPreview($limit) ?: 'No text preview is available for this project yet.';
    }

    public function pdfTextPreview(int $limit = 1200, bool $preserveLayout = false): string
    {
        $source = $this->pdf_text ?: $this->abstract ?: $this->keywords;

        if (! $source) {
            return '';
        }

        $source = $preserveLayout
            ? $this->normalizePreviewLayout($source)
            : Str::of($source)->squish()->toString();
        $lowerSource = Str::lower($source);
        $preferredHeadings = $preserveLayout
            ? ['table of contents', 'introduction', 'background', 'abstract', 'overview']
            : ['introduction', 'background', 'abstract', 'overview'];

        foreach ($preferredHeadings as $heading) {
            $position = mb_stripos($lowerSource, $heading);

            if ($position !== false) {
                $source = mb_substr($source, $position);
                break;
            }
        }

        if (! $preserveLayout) {
            return Str::of($source)->squish()->limit($limit)->toString();
        }

        $source = $this->restoreLikelyDocumentLines($source);

        return $this->limitPreviewLayout($source, $limit);
    }

    private function normalizePreviewLayout(string $source): string
    {
        $source = str_replace(["\r\n", "\r"], "\n", $source);
        $source = preg_replace('/[ \t]+$/m', '', $source) ?? $source;
        $source = preg_replace("/\n{4,}/", "\n\n\n", $source) ?? $source;

        return trim($source);
    }

    private function restoreLikelyDocumentLines(string $source): string
    {
        if (substr_count($source, "\n") >= 2) {
            return $source;
        }

        $source = Str::of($source)->squish()->toString();
        $source = preg_replace(
            '/\s+(?=(CERTIFICATION|DEDICATION|ACKNOWLEDGEMENT|ABSTRACT|TABLE OF CONTENTS|LIST OF FIGURES|LIST OF TABLES|CHAPTER\s+[A-Z]+|INTRODUCTION|LITERATURE REVIEW)\b)/i',
            "\n",
            $source
        ) ?? $source;
        $source = preg_replace('/\s+(?=(?:\d+\.)+\d*\s+[A-Z][A-Za-z])/', "\n", $source) ?? $source;
        $source = preg_replace("/\n{3,}/", "\n\n", $source) ?? $source;

        return trim($source);
    }

    private function limitPreviewLayout(string $source, int $limit): string
    {
        if (mb_strlen($source) <= $limit) {
            return $source;
        }

        $truncated = mb_substr($source, 0, $limit);
        $lastBreak = mb_strrpos($truncated, "\n");

        if ($lastBreak !== false && $lastBreak > (int) ($limit * 0.55)) {
            $truncated = mb_substr($truncated, 0, $lastBreak);
        }

        return rtrim($truncated)."\n...";
    }

    public function scopeFiltered($query, array $filters)
    {
        $search = trim((string) ($filters['q'] ?? $filters['search'] ?? ''));

        if ($search !== '') {
            $terms = self::searchTerms($search) ?: [$search];

            $query->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $like = "%{$term}%";

                    $query->orWhere('title', 'like', $like)
                        ->orWhere('student_name', 'like', $like)
                        ->orWhere('supervisor', 'like', $like)
                        ->orWhere('project_type', 'like', $like)
                        ->orWhere('completion_year', 'like', $like)
                        ->orWhere('abstract', 'like', $like)
                        ->orWhere('keywords', 'like', $like)
                        ->orWhere('pdf_text', 'like', $like)
                        ->orWhereHas('category', function ($query) use ($like) {
                            $query->where('name', 'like', $like);
                        })
                        ->orWhereHas('tags', function ($query) use ($like) {
                            $query->where('name', 'like', $like);
                        });
                }
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['tag_id'])) {
            $query->whereHas('tags', function ($query) use ($filters) {
                $query->where('tags.id', $filters['tag_id']);
            });
        }

        if (! empty($filters['project_type'])) {
            $query->where('project_type', $filters['project_type']);
        }

        if (! empty($filters['year_from'])) {
            $query->where('completion_year', '>=', (int) $filters['year_from']);
        }

        if (! empty($filters['year_to'])) {
            $query->where('completion_year', '<=', (int) $filters['year_to']);
        }

        return $query;
    }

    public function scopeSorted($query, ?string $sort)
    {
        return match ($sort) {
            'relevance' => $query->latest('id'),
            'oldest' => $query->oldest(),
            'title' => $query->orderBy('title'),
            'year_asc' => $query->orderBy('completion_year')->latest('id'),
            'year_desc' => $query->orderByDesc('completion_year')->latest('id'),
            default => $query->latest(),
        };
    }

    public function scopeRankedForSearch($query, ?string $search)
    {
        $terms = self::searchTerms($search);

        if (! $terms) {
            return $query;
        }

        $scoreParts = [];
        $bindings = [];

        foreach ($terms as $term) {
            $like = "%{$term}%";
            $scoreParts[] = 'CASE WHEN title LIKE ? THEN 30 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN keywords LIKE ? THEN 20 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN abstract LIKE ? THEN 14 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN project_type LIKE ? THEN 10 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN student_name LIKE ? THEN 8 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN supervisor LIKE ? THEN 8 ELSE 0 END';
            $scoreParts[] = 'CASE WHEN pdf_text LIKE ? THEN 6 ELSE 0 END';
            array_push($bindings, $like, $like, $like, $like, $like, $like, $like);
        }

        return $query->orderByRaw('('.implode(' + ', $scoreParts).') DESC', $bindings);
    }

    public static function searchTerms(?string $search): array
    {
        return collect(preg_split('/[\s,;|]+/', trim((string) $search), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($term) => trim($term))
            ->filter(fn ($term) => mb_strlen($term) >= 2)
            ->unique(fn ($term) => mb_strtolower($term))
            ->take(8)
            ->values()
            ->all();
    }

    public function pdfAbsolutePath(): ?string
    {
        if ($this->pdf_url) {
            return null;
        }

        if ($this->pdf_path && Storage::disk('local')->exists($this->pdf_path)) {
            return Storage::disk('local')->path($this->pdf_path);
        }

        if ($this->pdf_file) {
            $legacyPath = public_path('uploads/'.$this->pdf_file);

            if (is_file($legacyPath)) {
                return $legacyPath;
            }
        }

        return null;
    }

    public function pdfSourceUrl(): ?string
    {
        return $this->pdf_download_url ?: $this->pdf_url;
    }

    public function hasPdf(): bool
    {
        return (bool) ($this->pdfSourceUrl() || $this->pdfAbsolutePath());
    }

    public function pdfDisplayName(): string
    {
        return $this->pdf_original_name ?: ($this->pdf_file ?: str($this->title)->slug()->append('.pdf')->toString());
    }
}
