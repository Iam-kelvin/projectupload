<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'student_name' => ['required', 'string', 'max:255'],
            'supervisor' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'abstract' => ['nullable', 'string', 'max:8000'],
            'keywords' => ['nullable', 'string', 'max:1000'],
            'completion_year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'pdf_file' => ['required_without:cloud_pdf_url', 'file', 'mimes:pdf', 'max:51200'],
            'cloud_pdf_url' => ['nullable', 'required_without:pdf_file', 'url', 'max:2048'],
            'cloud_pdf_download_url' => ['nullable', 'url', 'max:2048'],
            'cloud_pdf_storage_key' => ['nullable', 'string', 'max:1000'],
            'cloud_pdf_original_name' => ['nullable', 'string', 'max:255'],
            'cloud_pdf_mime' => ['nullable', 'string', 'max:120'],
            'cloud_pdf_size' => ['nullable', 'integer', 'min:1'],
            'cloud_file_hash' => ['nullable', 'string', 'size:64'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'new_tags' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
