<?php

namespace App\Inertia\Support\Options;

use App\Models\Directories\Division;

final class DivisionOptions
{
    public function get(): array
    {
        return Division::query()
            ->select(['id', 'short_name', 'report_position'])
            ->orderBy('report_position')
            ->get()
            ->map(fn(Division $division) => [
                'label' => $division->short_name,
                'value' => $division->id,
            ])
            ->toArray();
    }
}
