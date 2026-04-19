<?php

namespace App\Inertia\Incidents\Actions;

use App\Inertia\Incidents\DTO\IncidentFiltersData;
use App\Models\Incident\Incident;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class GetIncidentTableAction
{
    public function handle(
        IncidentFiltersData $filters,
        array               $queryParams = [],
    ): LengthAwarePaginator
    {
        return QueryBuilder::for(
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
            ->allowedSorts(['datetime_incident'])
            ->defaultSort('-datetime_incident')
            ->paginate($filters->normalizedPerPage())
            ->appends($queryParams);

    }
}
