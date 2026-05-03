<?php

namespace App\Inertia\Incidents\DTO;

use App\Inertia\Incidents\Requests\IncidentIndexRequest;
use Spatie\LaravelData\Data;

class IncidentTableParamsData extends Data
{
    public function __construct(
        public int $per_page = 15,
        public int $page = 1,
        public ?string $sort = null,
        public array $filter = [],
    )
    {
    }

    public static function fromRequest(IncidentIndexRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            per_page: (int)($validated['per_page'] ?? 15),
            page: (int)($validated['page'] ?? 1),
            sort: $validated['sort'] ?? null,
            filter: $validated['filter'] ?? [],
        );
    }

    public function normalizedPerPage(): int
    {
        return in_array($this->per_page, [10, 15, 25, 50, 100], true)
            ? $this->per_page
            : 15;
    }
}
