<?php

namespace App\Services\Incident\Reports\IncidentsReportsConfigEnum;

use Filament\Support\Contracts\HasLabel;

enum TypeIncidentsStrategy: string implements HasLabel
{
    case OnlyNnrIncidents = 'only_nnr_incidents';
    case AllIncidents = 'all_incidents';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::OnlyNnrIncidents => 'Только ННР',
            self::AllIncidents => 'Все'
        };
    }

    public function getTypeSummaryReportStrategy(): TypeSummaryReportStrategy
    {
        return match ($this) {
            self::OnlyNnrIncidents => TypeSummaryReportStrategy::NnrIncidentsForPeriod,
            self::AllIncidents => TypeSummaryReportStrategy::IncidentsForPeriod,
        };
    }
}
