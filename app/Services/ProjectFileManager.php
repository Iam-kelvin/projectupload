<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
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

    public function storeCloudUpload(array $validated, ?Project $project = null): array
    {
        $url = $validated['cloud_pdf_url'] ?? null;
        $hash = $validated['cloud_file_hash'] ?? null;

        if (! $url) {
            throw ValidationException::withMessages([
                'pdf_file' => 'Upload the PDF before saving the project.',
            ]);
        }

        $duplicate = Project::withTrashed()
            ->when($hash, fn ($query) => $query->where('file_hash', $hash))
            ->when(! $hash, fn ($query) => $query->where('pdf_url', $url))
            ->when($project, fn ($query) => $query->whereKeyNot($project->getKey()))
            ->first();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'pdf_file' => 'This PDF already exists in the repository.',
            ]);
        }

        return [
            'pdf_file' => basename(parse_url($url, PHP_URL_PATH) ?: 'project.pdf'),
            'pdf_path' => null,
            'pdf_storage_disk' => 'vercel_blob',
            'pdf_storage_key' => $validated['cloud_pdf_storage_key'] ?? null,
            'pdf_url' => $url,
            'pdf_download_url' => $validated['cloud_pdf_download_url'] ?? $url,
            'pdf_original_name' => $validated['cloud_pdf_original_name'] ?? basename(parse_url($url, PHP_URL_PATH) ?: 'project.pdf'),
            'pdf_mime' => $validated['cloud_pdf_mime'] ?? 'application/pdf',
            'pdf_size' => $validated['cloud_pdf_size'] ?? null,
            'file_hash' => $hash,
            'pdf_text' => $this->extractRemoteText($url, (int) ($validated['cloud_pdf_size'] ?? 0)),
        ];
    }

    public function delete(Project $project): void
    {
        if ($project->pdf_path && Storage::disk('local')->exists($project->pdf_path)) {
            Storage::disk('local')->delete($project->pdf_path);
        }
    }

    private function extractRemoteText(string $url, int $size): string
    {
        $maxBytes = (int) config('services.pdf_remote_extract_max_bytes', 31457280);

        if ($size > $maxBytes) {
            return '';
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'project-pdf-');

        if (! $temporaryPath) {
            return '';
        }

        try {
            $response = Http::timeout(45)->sink($temporaryPath)->get($url);

            if (! $response->successful() || filesize($temporaryPath) > $maxBytes) {
                return '';
            }

            return $this->extractor->extract($temporaryPath);
        } catch (\Throwable) {
            return '';
        } finally {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
    }
}
