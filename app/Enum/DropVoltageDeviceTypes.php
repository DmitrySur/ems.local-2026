<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum DropVoltageDeviceTypes: string implements HasLabel
{
    case VSH = 'ВШ';
    case VU = 'ВУ';
    case SU = 'СУ';
    case VSB = 'ВСБ';
    case MV = 'МВ';
    case SKV = 'СКВ';
    case OTHER = 'Прочее';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::VSH => 'ВШ',
            self::VU => 'ВУ',
            self::SU => 'СУ',
            self::VSB => 'ВСБ',
            self::MV => 'МВ',
            self::SKV => 'СКВ',
            self::OTHER => 'Прочее',
        };
    }
}
