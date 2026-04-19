<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum IncidentStatuses: string implements HasLabel
{
    case InWorking = 'in_working';
    case InRepair = 'in_repair';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::InRepair => 'В ремонте',
            self::InWorking => 'Закрыт',
        };
    }
}
