<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DropVoltageDeviceStatuses: string implements HasLabel, HasColor
{
    case Checked = 'checked';
    case Unchecked = 'unchecked';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Checked => 'Проверено',
            self::Unchecked => 'Не проверено',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Checked => 'success',
            self::Unchecked => 'danger',
        };
    }
}
