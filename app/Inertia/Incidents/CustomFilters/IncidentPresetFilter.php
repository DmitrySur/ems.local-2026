<?php

namespace App\Inertia\Incidents\CustomFilters;

use App\Enum\IncidentStatuses;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class IncidentPresetFilter implements Filter
{
    public const DEFAULT = 'unresolved_all';

    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        $value = is_array($value) ? reset($value) : $value;

        return match ((string)$value) {
            'unresolved_all' => $this->unresolved($query),
            // Все не устраненные за сутки
            'unresolved_day' => $this->unresolved(
                $this->forOperationalDay($query)
            ),

            // ННР все
            'nnr_all' => $this->nnr($query),

            // ННР не устраненные
            'nnr_unresolved' => $this->unresolved(
                $this->nnr($query)
            ),

            // Все за сутки
            'day_all' => $this->forOperationalDay($query),

            // Все инциденты, без фильтров
            'all' => $query,

            default => $this->unresolved($query),
        };
    }

    private function unresolved(Builder $query): Builder
    {
        return $query->where(
            'status_resolution',
            IncidentStatuses::InRepair->value
        );
    }

    private function nnr(Builder $query): Builder
    {
        return $query->where('incident_classification', 'ННР');
    }

    private function forOperationalDay(Builder $query): Builder
    {
        [$from, $to] = $this->operationalDayPeriod();

        return $query->whereBetween('datetime_incident', [$from, $to]);
    }

    private function operationalDayPeriod(): array
    {
        return [
            Carbon::today()->subDay()->setTime(8, 0),
            Carbon::now(),
        ];
    }
}
