<?php

namespace App\Inertia\Incidents\Actions;

use App\Inertia\Incidents\CustomFilters\IncidentPresetFilter;
use App\Inertia\Incidents\DTO\IncidentTableParamsData;
use App\Models\Incident\Incident;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetIncidentTableAction
{
    public function handle(
        IncidentTableParamsData $filters,
        array                   $queryParams = [],
    ): LengthAwarePaginator
    {
        $query = QueryBuilder::for(
            Incident::query()
                ->with([
                        'objectInfrastructure:id,name,type',
                        'incidentType:id,title,has_directory_objects',
                        'division:id,short_name',
                        'ituSpecie:id,title',
                        'ituDirectoryObject:id,itu_specie_id,title',
                        'ituDirectoryObject.ituSpecie:id,title',
                        'ituCharacteristic:id,title',
                        'ituFault:id,title',
                        'ituElement:id,title',
                        'ituReasonBreakdown:id,title',
                        'creator:id,name',
                        'editor:id,name',
                    ]
                ))
            ->allowedFilters([
                AllowedFilter::exact('search_by_number', 'id'),
                AllowedFilter::exact('division_id'),
                AllowedFilter::exact('incident_type_id'),
                AllowedFilter::exact('object_infrastructure_id'),
                AllowedFilter::custom('preset_filter', new IncidentPresetFilter())
                    ->default(IncidentPresetFilter::DEFAULT),

            ])
            ->allowedSorts(['datetime_incident'])
            ->defaultSort('-datetime_incident');
        return $query
            ->paginate($filters->normalizedPerPage())
            ->appends($queryParams);

    }
}
