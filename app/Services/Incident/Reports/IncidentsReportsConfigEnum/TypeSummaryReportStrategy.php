<?php

namespace App\Services\Incident\Reports\IncidentsReportsConfigEnum;

use Filament\Support\Contracts\HasLabel;

enum TypeSummaryReportStrategy: string implements HasLabel
{
    case IncidentsForPeriod = 'incidents_for_period';
    case IncidentsInRepair = 'incidents_in_repair';
    case NnrIncidentsForPeriod = 'nnr_incidents_for_period';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::IncidentsForPeriod => 'Все инциденты за период',
            self::IncidentsInRepair => 'В ремонте',
            self::NnrIncidentsForPeriod => 'ННР инциденты за период',
        };
    }

    public function typeIncidentsStrategy(): TypeIncidentsStrategy
    {
        return match ($this) {
            self::IncidentsForPeriod, self::IncidentsInRepair => TypeIncidentsStrategy::AllIncidents,
            self::NnrIncidentsForPeriod => TypeIncidentsStrategy::OnlyNnrIncidents,
        };
    }

    public function getDropsVoltageStrategy(): DropsVoltageStrategy
    {
        return match ($this) {
            self::IncidentsInRepair, self::NnrIncidentsForPeriod => DropsVoltageStrategy::WithoutDropsVoltage,
            self::IncidentsForPeriod => DropsVoltageStrategy::WithDropsVoltage,
        };
    }

    public function getInspectionStrategy(): InspectionsStrategy
    {
        return match ($this) {
            self::IncidentsInRepair, self::NnrIncidentsForPeriod => InspectionsStrategy::WithoutInspections,
            self::IncidentsForPeriod => InspectionsStrategy::WithInspections,
        };
    }
}
