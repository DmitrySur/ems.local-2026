<?php

namespace App\Inertia\Incidents\ViewModel;

use App\Inertia\Incidents\DTO\IncidentTableParamsData;
use App\Inertia\Incidents\JsonResources\IncidentTableResource;
use App\Inertia\Support\Options\DivisionOptions;
use App\Inertia\Support\Options\IncidentTypesOptions;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IncidentIndexViewModel
{
    public function __construct(
        private LengthAwarePaginator    $paginator,
        private IncidentTableParamsData $filters,
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
                'sort' => $this->filters->sort,
                'filter' => $this->filters->filter,
            ],
            'options' => [
                'per_page' => [10, 15, 25, 50, 100],
                'divisions' => app(DivisionOptions::class)->get(),
                'incident_types' => app(IncidentTypesOptions::class)->get(),
            ]
        ];
    }

}
