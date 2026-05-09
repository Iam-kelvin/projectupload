<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectFileManager
{
    public function __construct(private readonly PdfTextExtractor $extractor)
    {
    }

    public function store(UploadedFile $file, ?Project $project = null): array
    {
        $hash = hash_file('sha256', $file->getRealPath());
        $duplicate = Project::withTrashed()
            ->where('file_hash', $hash)
            ->when($project, fn ($query) => $query->whereKeyNot($project->getKey()))
            ->first();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'pdf_file' => 'This PDF already exists in the repository.',
            ]);
        }

        $fileName = (string) Str::uuid().'.pdf';
        $path = Storage::disk('local')->putFileAs('projects', $file, $fileName);

        return [
            'pdf_file' => $fileName,
            'pdf_path' => $path,
            'pdf_original_name' => $file->getClientOriginalName(),
            'pdf_mime' => $file->getClientMimeType(),
            'pdf_size' => $file->getSize(),
            'file_hash' => $hash,
            'pdf_text' => $this->extractor->extract($file),
        ];
    }

    public function delete(Project $project): void
    {
        if ($project->pdf_path && Storage::disk('local')->exists($project->pdf_path)) {
            Storage::disk('local')->delete($project->pdf_path);
        }
    }
}
