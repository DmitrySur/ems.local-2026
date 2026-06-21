<?php

namespace App\Inertia\ApiSupportControllers;

use App\Http\Controllers\Controller;
use App\Models\Directories\ObjectInfrastructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiSupportObjectInfrastructureSelectController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Валидируем входящие параметры.
        $validated = $request->validate([
            // Одно выбранное значение: ID объекта инфраструктуры.
            'filter.object_infrastructure_id' => [
                'nullable',
                'integer',
                'exists:object_infrastructures,id',
            ],

            // Строка поиска: русские буквы, цифры, пробел, дефис, точка.
            'filter.object_infrastructure_name' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[А-Яа-яЁё0-9 .-]*$/u',
            ],
        ]);

        $selectedId = $validated['filter']['object_infrastructure_id'] ?? null;

        $name = trim(
            (string)($validated['filter']['object_infrastructure_name'] ?? '')
        );

        $items = ObjectInfrastructure::query()
            ->select([
                'id',
                'name',
                'type',
                'group_infrastructure_object_id',
            ])
            ->with('groupInfrastructureObject:id,short_title')
            ->when($selectedId, function ($query) use ($selectedId) {
                // Если фильтр пришёл из URL, подгружаем выбранный объект по id.
                $query->where('id', $selectedId);
            }, function ($query) use ($name) {
                // Иначе ищем варианты по названию объекта.
                $query->when($name !== '', function ($query) use ($name) {
                    $query->where('name', 'like', "%$name%");
                });
            })
            ->orderBy('type')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn($item) => [
                // value select = ID объекта инфраструктуры.
                'id' => $item->id,
                'value' => $item->id,
                // label select
                'label' => $item->name,
                // type нужен только для выбора иконки.
                'type' => $item->type,
                // short_name нужен только для цвета иконки.
                'short_name' => $item->groupInfrastructureObject?->short_title,
                //group_infrastructure_object_id для формы инцидентов
                'group_infrastructure_object_id' => $item->group_infrastructure_object_id ?? null
            ]);

        return response()->json([
            'data' => $items,
        ]);
    }
}
