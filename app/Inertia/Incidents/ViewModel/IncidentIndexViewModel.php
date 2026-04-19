<?php

namespace App\Inertia\Incidents\ViewModel;

use App\Inertia\Incidents\DTO\IncidentFiltersData;
use App\Inertia\Incidents\JsonResources\IncidentTableResource;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IncidentIndexViewModel
{
    public function __construct(
        private LengthAwarePaginator $paginator,
        private IncidentFiltersData  $filters,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'table' => [
                'data' => IncidentTableResource::collection(collect($this->paginator->items()))->resolve(),
                'meta' => [
                    'current_page' => $this->paginator->currentPage(),
                    'last_page' => $this->paginator->lastPage(),
                    'per_page' => $this->paginator->perPage(),
                    'total' => $this->paginator->total(),
                ],
            ],
            'filters' => [
                'per_page' => $this->filters->normalizedPerPage(),
                'page' => $this->filters->page,
            ],
            'options' => [
                'per_page' => [10, 25, 50, 100],
            ]
        ];
    }

}
