<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum InspectionsTypes: string implements HasLabel
{
    case NightInspection = 'night_inspection';
    case DaySecurity = 'day_security';

    case SurpriseInspection = 'surprise_inspection';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NightInspection => 'Ночная проверка',
            self::DaySecurity => 'День безопасности',
            self::SurpriseInspection => 'Внезапная проверка'
        };
    }
}
