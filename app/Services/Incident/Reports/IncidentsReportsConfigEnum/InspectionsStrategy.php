<?php

namespace App\Services\Incident\Reports\IncidentsReportsConfigEnum;

use Filament\Support\Contracts\HasLabel;

enum InspectionsStrategy: string implements HasLabel
{
    case WithInspections = 'with_inspections';
    case WithoutInspections = 'without_drops_voltage';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::WithInspections => 'С проверками',
            self::WithoutInspections => 'Без проверок'
        };
    }
}
