<?php

namespace App\Inertia\Incidents\Actions;

use App\Enum\IncidentStatuses;
use App\Models\Directories\DispatchArea;
use App\Models\Directories\Division;
use App\Models\Directories\IncidentType;
use App\Models\Directories\ItuCharacteristic;
use App\Models\Directories\ItuDirectoryObject;
use App\Models\Directories\ItuElement;
use App\Models\Directories\ItuFault;
use App\Models\Directories\ItuReasonBreakdown;
use App\Models\Directories\ItuSpecie;
use App\Models\User;
use Illuminate\Support\Collection;

class GetIncidentOpenedFormAction
{
    /**
     * Формируем данные, которые нужны Vue-форме при открытии модального окна создания.
     *
     * Важно:
     * - object_infrastructure_id здесь НЕ грузим;
     * - объект инфраструктуры будет работать через отдельный async select;
     * - здесь отдаем только локальные справочники и значения по умолчанию.
     */
    public function handle(User $user): array
    {
        return [
            'defaults' => $this->defaults(),
            'options' => $this->options(),
            'permissions' => $this->permissions($user),
        ];
    }

    /**
     * Значения формы по умолчанию.
     */
    private function defaults(): array
    {
        return [
            'incident' => [
                // Ant Design на фронте потом преобразует эту строку в dayjs.
                'datetime_incident' => now()->format('Y-m-d H:i:s'),

                'object_infrastructure_id' => null,
                'location' => null,
                'detail_location' => null,
                'reported_by' => null,
                'division_id' => null,

                'incident_type_id' => null,
                'itu_specie_id' => null,
                'itu_characteristic_id' => null,
                'itu_directory_object_id' => null,
                'itu_fault_id' => null,
                'itu_element_id' => null,
                'itu_reason_breakdown_id' => null,

                'detail_object_incident' => null,
                'detail_incident' => null,

                'incident_classification' => 'ННР',
                'number_nnr' => null,

                'appropriate_measures' => null,

                // Старый Filament ставил по умолчанию InRepair.
                'status_resolution' => IncidentStatuses::InRepair->value,

                // В БД у поля status_incident есть default opened,
                // но на фронт тоже отдадим явно.
                'status_incident' => 'opened',

                'repair_date' => null,

                'dispatch_area_id' => null,

                // В новой Vue-форме используем именно is_in_report,
                // а не старое имя feedback из Filament.
                'is_in_report' => true,

                'note' => null,
            ],

            // В режиме создания строки этих таблиц живут локально во Vue.
            'referral_workers' => [],
            'information_workers' => [],
            'chronicles' => [],
        ];
    }

    /**
     * Справочники для формы.
     */
    /**
     * Справочники для формы.
     */
    private function options(): array
    {
        return [
            'locations' => $this->simpleStringOptions(config('incident_locations', [])),

            'short_positions' => $this->simpleStringOptions(config('incident_short_positions', [])),

            'incident_classifications' => [
                ['value' => 'ННР', 'label' => 'ННР'],
                ['value' => 'ТО', 'label' => 'ТО'],
                ['value' => 'АРМ', 'label' => 'АРМ'],
            ],

            'incident_statuses' => $this->incidentStatusOptions(),

            'divisions' => $this->divisionOptions(),

            'dispatch_areas' => $this->dispatchAreaOptions(),

            'incident_types' => $this->incidentTypeOptions(),

            'itu_species' => $this->ituSpecieOptions(),

            'itu_characteristics' => $this->directoryOptions(
                ItuCharacteristic::query()
                    ->select(['id', 'title', 'incident_type_id'])
                    ->orderBy('title')
                    ->get()
            ),

            'itu_faults' => $this->directoryOptions(
                ItuFault::query()
                    ->select(['id', 'title', 'incident_type_id'])
                    ->orderBy('title')
                    ->get()
            ),

            'itu_elements' => $this->directoryOptions(
                ItuElement::query()
                    ->select(['id', 'title', 'incident_type_id'])
                    ->orderBy('title')
                    ->get()
            ),

            'itu_reason_breakdowns' => $this->directoryOptions(
                ItuReasonBreakdown::query()
                    ->select(['id', 'title', 'incident_type_id'])
                    ->orderBy('title')
                    ->get()
            ),

            'itu_directory_objects' => $this->ituDirectoryObjectOptions(),
        ];
    }

    /**
     * Права текущего пользователя для формы.
     */
    private function permissions(User $user): array
    {
        return [
            // В старой Filament-форме dispatch_area_id был disabled
            // для всех, кроме admin и senior_dispatcher.
            'can_change_dispatch_area' => $user->hasRole(['admin', 'senior_dispatcher']),

            // Создание объекта ИТУ из select пока не делаем в первом этапе.
            'can_create_itu_directory_object' => false,
        ];
    }

    /**
     * Простой справочник строк:
     * ['А', 'Б'] -> [{ value: 'А', label: 'А' }, { value: 'Б', label: 'Б' }]
     */
    private function simpleStringOptions(array $items): array
    {
        return collect($items)
            ->filter(fn($item) => filled($item))
            ->map(fn($item) => [
                'value' => $item,
                'label' => $item,
            ])
            ->values()
            ->all();
    }

    /**
     * Статусы инцидента из enum.
     *
     * Если в enum есть метод getLabel(), используем его.
     * Если нет — показываем value.
     */
    private function incidentStatusOptions(): array
    {
        return collect(IncidentStatuses::cases())
            ->map(function (IncidentStatuses $status) {
                return [
                    'value' => $status->value,
                    'label' => method_exists($status, 'getLabel')
                        ? $status->getLabel()
                        : $status->value,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Подразделения.
     *
     * На фронте потом будем фильтровать их по выбранному object_infrastructure_id.
     */
    private function divisionOptions(): array
    {
        return Division::query()
            ->select([
                'id',
                'name',
                'short_name',
                'has_group_object',
                'group_infrastructure_object_id',
            ])
            ->orderBy('short_name')
            ->get()
            ->map(fn(Division $division) => [
                'value' => $division->id,
                'label' => $division->short_name ?: $division->name,

                // meta нужен, чтобы Vue мог фильтровать подразделения
                // по группе выбранного объекта инфраструктуры.
                'meta' => [
                    'name' => $division->name,
                    'short_name' => $division->short_name,
                    'has_group_object' => (bool)$division->has_group_object,
                    'group_infrastructure_object_id' => $division->group_infrastructure_object_id,
                ],
            ])
            ->values()
            ->all();
    }

    /**
     * Диспетчерские участки.
     *
     * Используем связи из моделей:
     * - DispatchArea belongsToMany GroupInfrastructureObject
     * - GroupInfrastructureObject belongsToMany DispatchArea
     */
    private function dispatchAreaOptions(): array
    {
        return DispatchArea::query()
            ->with([
                // Загружаем группы объектов, связанные с участком через pivot.
                'groupInfrastructureObjects:id',
            ])
            ->select([
                'id',
                'name',
                'group_infrastructure_object_id',
            ])
            ->orderBy('name')
            ->get()
            ->map(function (DispatchArea $dispatchArea) {
                /**
                 * Группы из связи многие-ко-многим.
                 * Это основной вариант, потому что старая Filament-форма
                 * использует именно groupInfrastructureObject->dispatchAreas().
                 */
                $groupIdsFromRelation = $dispatchArea
                    ->groupInfrastructureObjects
                    ->pluck('id');

                /**
                 * Группа из прямого поля dispatch_areas.group_infrastructure_object_id.
                 */
                $groupIdsFromDirectField = collect([
                    $dispatchArea->group_infrastructure_object_id,
                ])->filter();

                /**
                 * Итоговый список групп участка.
                 */
                $groupIds = $groupIdsFromRelation
                    ->merge($groupIdsFromDirectField)
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();

                return [
                    'value' => $dispatchArea->id,
                    'label' => $dispatchArea->name,

                    'meta' => [
                        // Для старой простой логики.
                        'group_infrastructure_object_id' => $groupIds[0] ?? null,

                        // Главное поле для Vue.
                        'group_infrastructure_object_ids' => $groupIds,
                    ],
                ];
            })
            ->values()
            ->all();
    }


    /**
     * Типы инцидентов.
     *
     * В meta отдаем флаги, от которых зависит показ ИТУ-полей.
     */
    private function incidentTypeOptions(): array
    {
        return IncidentType::query()
            ->select([
                'id',
                'title',
                'has_characteristic',
                'has_elements',
                'has_faults',
                'has_directory_objects',
                'has_species',
                'reported_by_list',
            ])
            ->orderBy('title')
            ->get()
            ->map(fn(IncidentType $incidentType) => [
                'value' => $incidentType->id,
                'label' => $incidentType->title,
                'meta' => [
                    'has_species' => (bool)$incidentType->has_species,
                    'has_characteristic' => (bool)$incidentType->has_characteristic,
                    'has_directory_objects' => (bool)$incidentType->has_directory_objects,
                    'has_faults' => (bool)$incidentType->has_faults,
                    'has_elements' => (bool)$incidentType->has_elements,
                    'reported_by_list' => $this->normalizeReportedByList($incidentType->reported_by_list),
                ],
            ])
            ->values()
            ->all();
    }

    /**
     * Виды ИТУ.
     */
    private function ituSpecieOptions(): array
    {
        return ItuSpecie::query()
            ->select([
                'id',
                'title',
                'incident_type_id',
                'has_directory_objects',
            ])
            ->orderBy('title')
            ->get()
            ->map(fn(ItuSpecie $specie) => [
                'value' => $specie->id,
                'label' => $specie->title,
                'incident_type_id' => $specie->incident_type_id,
                'has_directory_objects' => (bool)$specie->has_directory_objects,
            ])
            ->values()
            ->all();
    }

    /**
     * Общий формат для простых ИТУ-справочников:
     * характеристики, неисправности, элементы, причины.
     */
    private function directoryOptions(Collection $items): array
    {
        return $items
            ->map(fn($item) => [
                'value' => $item->id,
                'label' => $item->title,
                'incident_type_id' => $item->incident_type_id,
            ])
            ->values()
            ->all();
    }

    /**
     * Объекты ИТУ.
     *
     * Label сразу формируем как "Вид - Объект",
     * чтобы на фронте не собирать это вручную.
     */
    private function ituDirectoryObjectOptions(): array
    {
        return ItuDirectoryObject::query()
            ->select([
                'id',
                'title',
                'incident_type_id',
                'itu_specie_id',
            ])
            ->with('ituSpecie:id,title')
            ->orderBy('title')
            ->get()
            ->map(fn(ItuDirectoryObject $object) => [
                'value' => $object->id,
                'label' => trim(($object->ituSpecie?->title ? $object->ituSpecie->title . ' - ' : '') . $object->title),
                'title' => $object->title,
                'incident_type_id' => $object->incident_type_id,
                'itu_specie_id' => $object->itu_specie_id,
            ])
            ->values()
            ->all();
    }

    /**
     * reported_by_list может быть:
     * - массивом;
     * - json-строкой;
     * - null.
     *
     * Приводим к обычному массиву строк.
     */
    private function normalizeReportedByList(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if (is_string($value) && filled($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded)
                ? array_values($decoded)
                : [];
        }

        return [];
    }


}