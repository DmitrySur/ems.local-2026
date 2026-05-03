<?php

namespace App\Inertia\Support\Options;

use App\Models\Directories\IncidentType;

final class IncidentTypesOptions
{
    public function get(): array
    {
        return IncidentType::query()
            ->select(['id', 'title', 'report_position'])
            ->orderBy('report_position')
            ->get()
            ->map(fn(IncidentType $division) => [
                'label' => $division->title,
                'value' => $division->id,
            ])
            ->toArray();
    }
}
