<?php

namespace App\Inertia\Incidents\Controllers;

use App\Http\Controllers\Controller;
use App\Inertia\Incidents\Actions\GetIncidentTableAction;
use App\Inertia\Incidents\DTO\IncidentTableParamsData;
use App\Inertia\Incidents\Requests\IncidentIndexRequest;
use App\Inertia\Incidents\ViewModel\IncidentIndexViewModel;
use Inertia\Inertia;
use Inertia\Response;

class IncidentsInertiaController extends Controller
{
    public function index(
        IncidentIndexRequest   $request,
        GetIncidentTableAction $getIncidentTableAction
    ): Response
    {
        request()->query->replace($request->validated());

        $filters = IncidentTableParamsData::fromRequest($request);

        $paginator = $getIncidentTableAction->handle(
            filters: $filters,
            queryParams: $request->query()
        );

        $vm = new IncidentIndexViewModel($paginator, $filters);

        return Inertia::render('Incidents/IndexIncidents', $vm->toArray());
    }
}
