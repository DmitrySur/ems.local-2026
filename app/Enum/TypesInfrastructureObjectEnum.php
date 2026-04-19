<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum TypesInfrastructureObjectEnum: string implements HasLabel
{
    case Driving = 'driving';
    case Station = 'station';
    case EndRoad = 'end_road';
    case BrandPath = 'brand_path';
    case Building = 'building';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Driving => 'Перегон',
            self::Station => 'Станция',
            self::EndRoad => 'Тупик',
            self::BrandPath => 'Ветка',
            self::Building => 'Наземный объект',
        };
    }

    public function getShortLabel(): ?string
    {
        return match ($this) {
            self::Driving => 'Перегон',
            self::Station => 'ст.',
            self::EndRoad, self::BrandPath, self::Building => '',
        };
    }
}
