<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum IncidentCardStatuses: string implements HasLabel
{
    case Opened = 'opened';
    case Closed = 'closed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Opened => 'Открыт',
            self::Closed => 'Закрыт',
        };
    }
}
