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
        $searchByNumber = $this->input('filter.search_by_number');
        if ($searchByNumber !== null && $searchByNumber !== '' && !ctype_digit((string)$searchByNumber)) {
            $filter = (array)$this->input('filter', []);
            $filter['search_by_number'] = null;
            $this->merge([
                'filter' => $filter,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', 'string', 'in:datetime_incident,-datetime_incident'],
            'filter' => ['nullable', 'array'],
            'filter.search_by_number' => ['nullable', 'integer'],
        ];
    }
}
