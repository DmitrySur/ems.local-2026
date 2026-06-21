<?php

namespace App\Inertia\Incidents\Controllers;

use App\Inertia\Incidents\Actions\GetIncidentOpenedFormAction;
use Illuminate\Http\JsonResponse;

class IncidentCreateFormController
{
    /**
     * Возвращает данные для открытия формы создания инцидента.
     *
     * Это НЕ сохранение инцидента.
     * Это только справочники, defaults и права пользователя.
     */
    public function __invoke(GetIncidentOpenedFormAction $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle(auth()->user()),
        ]);
    }
}