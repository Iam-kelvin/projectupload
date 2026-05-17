<?php

namespace App\Http\Requests;

class UpdateProjectRequest extends StoreProjectRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return (bool) $project?->canBeEditedBy($this->user());
    }

    public function rules(): array
    {
        $rules = parent::rules();
        $rules['pdf_file'] = ['nullable', 'file', 'mimes:pdf', 'max:51200'];

        return $rules;
    }
}
