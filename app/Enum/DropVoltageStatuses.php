<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum DropVoltageStatuses: string implements HasLabel
{
    case Opened = 'opened';
    case Closed = 'closed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Opened => 'Открыта',
            self::Closed => 'Закрыта',
        };
    }
}
