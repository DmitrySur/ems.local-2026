<?php

namespace App\Inertia\Incidents\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncidentIndexRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(
            [
                'per_page' => $this->input('per_page', 15),
                'page' => $this->input('page', 1),
            ],
        );
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
