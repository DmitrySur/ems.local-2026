<?php

namespace App\Exports;

use App\Enum\IncidentCardStatuses;
use App\Enum\IncidentStatuses;
use App\Enum\TypesInfrastructureObjectEnum;
use App\Models\Incident\Incident;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class IncidentsExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    protected array $datePeriod;

    public function __construct(array $datePeriod)
    {
        $this->datePeriod = $datePeriod;
    }


    public function collection(): Collection
    {
        return Incident::whereBetween('datetime_incident',
            [$this->datePeriod['startDate'], $this->datePeriod['endDate']])
            ->with([
                'dispatchArea',
                'division',
                'objectInfrastructure',
                'ituReasonBreakdown',
                'ituSpecie',
                'ituFault',
                'ituCharacteristic',
                'ituElement',
                'incidentType',
                'ituDirectoryObject',
                'creator',
                'editor'
            ])->get();

    }

    /**
     * @param Incident $row
     */
    public function map($row): array
    {
        $objectInfrastructure = TypesInfrastructureObjectEnum::tryFrom($row?->objectInfrastructure?->type)->getLabel() . ' ' . $row?->objectInfrastructure?->name;
        $itu = $row?->ituDirectoryObject?->ituSpecie?->title . ' ' . $row?->ituDirectoryObject?->title;
        $statusResolution = IncidentStatuses::tryFrom($row?->status_resolution)->getLabel();
        $statusIncident = IncidentCardStatuses::tryFrom($row?->status_incident)->getLabel();
        $createdBy = $row?->creator?->position . ' ' . $row?->creator?->name;
        $updatedBy = $row?->editor?->position . ' ' . $row?->editor?->name;
        return [
            Date::dateTimeToExcel($row?->datetime_incident ?? ''),
            $row?->division?->short_name ?? '',
            $row?->incidentType?->title ?? '',
            $row?->objectInfrastructure->groupInfrastructureObject->title,
            $objectInfrastructure,
            $row?->location ?? '',
            $row?->detail_location ?? '',
            $row?->reported_by ?? '',
            $row?->ituSpecie?->title ?? '',
            $row?->ituCharacteristic?->title ?? '',
            $itu,
            $row?->ituElement->title ?? '',
            $row?->ituFault->title ?? '',
            $row?->ituReasonBreakdown->title ?? '',
            $row?->detail_object_incident ?? '',
            $row?->detail_incident ?? '',
            $row?->incident_classification ?? '',
            $row?->number_nnr ?? '',
            $row?->appropriate_measures ?? '',
            $row?->repair_date ? Date::dateTimeToExcel($row->repair_date) : '',
            $statusResolution ?? '',
            $statusIncident ?? '',
            $row?->dispatchArea?->name ?? '',
            $row?->is_in_report ?? '',
            Date::dateTimeToExcel($row?->created_at),
            $createdBy,
            Date::dateTimeToExcel($row?->updated_at),
            $updatedBy,
        ];
    }

    public function headings(): array
    {
        return [
            'Дата и время',
            'Подразделение',
            'Тип инцидента',
            'Группа объектов',
            'Объект',
            'Локация',
            'Уточнение локации',
            'Сообшил',
            'Вид инцидента',
            'Характеристика',
            'ИТУ',
            'Элемент',
            'Неисправность',
            'Причина',
            'Уточнение объекта инцидента',
            'Уточнение инцидента',
            'Классификация инцидента',
            'Номер ННР',
            'Принятые меры',
            'Дата принятых мер',
            'Статус устранения',
            'Статус карточки',
            'ДП',
            'Отражение в сводке',
            'Дата и время создания',
            'Кто создал',
            'Дата и время редактирования',
            'Кто редактировал',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DATETIME,
            'Y' => NumberFormat::FORMAT_DATE_DATETIME,
            'T' => NumberFormat::FORMAT_DATE_DATETIME,
            'AA' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
