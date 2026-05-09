<?php

namespace App\Http\Requests;

class UpdateProjectRequest extends StoreProjectRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['pdf_file'] = ['nullable', 'file', 'mimes:pdf', 'max:10240'];

        return $rules;
    }
}
