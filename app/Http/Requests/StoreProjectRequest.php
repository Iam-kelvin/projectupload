<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->canAccessAdminPanel();
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
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'new_tags' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
