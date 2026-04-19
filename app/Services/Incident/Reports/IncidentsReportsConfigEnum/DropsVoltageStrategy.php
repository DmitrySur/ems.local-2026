<?php

namespace App\Services\Incident\Reports\IncidentsReportsConfigEnum;

use Filament\Support\Contracts\HasLabel;

enum DropsVoltageStrategy: string implements HasLabel
{
    case WithDropsVoltage = 'with_drops_voltage';
    case WithoutDropsVoltage = 'without_drops_voltage';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::WithDropsVoltage => 'С посадками напряжения',
            self::WithoutDropsVoltage => 'Без посадок напряжения'
        };
    }
}
