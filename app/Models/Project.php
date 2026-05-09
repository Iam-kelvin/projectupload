<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    public function scopeFiltered($query, array $filters)
    {
        $search = trim((string) ($filters['q'] ?? $filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $like = "%{$search}%";

                $query->where('title', 'like', $like)
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
            'oldest' => $query->oldest(),
            'title' => $query->orderBy('title'),
            'year_asc' => $query->orderBy('completion_year')->latest('id'),
            'year_desc' => $query->orderByDesc('completion_year')->latest('id'),
            default => $query->latest(),
        };
    }

    public function pdfAbsolutePath(): ?string
    {
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

    public function pdfDisplayName(): string
    {
        return $this->pdf_original_name ?: ($this->pdf_file ?: str($this->title)->slug()->append('.pdf')->toString());
    }
}
