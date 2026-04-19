<?php

namespace App\Services\Incident\Reports;

use App\Enum\DropVoltageStatuses;
use App\Enum\IncidentStatuses;
use App\Enum\InspectionsTypes;
use App\Enum\TypesInfrastructureObjectEnum;
use App\Models\Directories\Division;
use App\Models\Directories\ObjectInfrastructure;
use App\Models\DropVoltage\DropVoltage;
use App\Models\Incident\Incident;
use App\Models\Inspection\Inspection;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\LineSpacingRule;
use PhpOffice\PhpWord\Style\Font;
use Str;

class SummaryIncidentReportService
{
    protected ?PhpWord $wordDocument = null;

    protected ?Collection $collectionRecords = null;

    protected ?Collection $dropVoltageCollectionRecords = null;

    protected array $divisionsArray = [];

    protected ?string $periodString;

    protected ?string $startDate;

    //Начальная дата и время для отчета посадок
    protected ?Carbon $carbonStartDate;

    //Конечная дата и время для посадок
    protected ?Carbon $carbonEndDate;

    public function __construct()
    {
        $this->wordDocument = new PhpWord();
        $this->wordDocument->setDefaultFontName('Times New Roman');
        $this->wordDocument->setDefaultFontSize(12);
        $this->wordDocument->setDefaultParagraphStyle([
            'indentation' => [
                'firstLine' => 0,
            ],
            'spacingLineRule' => LineSpacingRule::EXACT,
            'spacing' => Converter::pointToTwip(14),
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'alignment' => Jc::CENTER,
        ]);
    }

    public function makeAndGetReportFilename(
        ?string $periodString,
        ?array $recordsIds,
        ?Carbon $carbonStartDate,
        ?Carbon $carbonEndDate,
    ): ?string {
        try {
            $this->collectionRecords = Collection::make(Incident::incidentsForMainReport(
                $recordsIds,
                $carbonStartDate,
                $carbonEndDate
            )->get())
                ->groupBy('division.name');
            $this->periodString = $periodString;
            $this->startDate = $carbonStartDate;
            $this->carbonStartDate = $carbonStartDate;
            $this->carbonEndDate = $carbonEndDate;
            $this->divisionsArray = Division::orderBy('report_position')
                ->pluck('name')
                ->toArray();
            $this->dropVoltageCollectionRecords = DropVoltage::whereBetween(
                'datetime_drop',
                [
                    $this->carbonStartDate->toDateTimeString(),
                    $this->carbonEndDate->toDateTimeString(),
                ]
            )
                ->with([
                    'groupInfrastructureObject.division',
                    'dropVoltageDevices',
                ])
                ->get()
                ->groupBy('groupInfrastructureObject.division.name');
            $this->makeMainContent();
            $filename = 'Сводка ' .
                Str::of($periodString)->replace(':', '-')->replace('период отчета ', '') .
                ' ' . now()->format('dmYHis') .
                Auth::id();
            $filepath = storage_path('/temp/' . $filename . '.docx');
            $writer = IOFactory::createWriter($this->wordDocument);
            $writer->save($filepath);

            return $filepath;
        } catch (Exception $exception) {
            return false;
        }
    }

    /** @noinspection PhpUndefinedMethodInspection */
    protected function makeMainContent(): void
    {
        $section = $this->wordDocument->addSection([
            'orientation' => 'landscape',
            'marginTop' => Converter::cmToTwip(1.5),
            'marginRight' => Converter::cmToTwip(1.5),
            'marginBottom' => Converter::cmToTwip(1.5),
            'marginLeft' => Converter::cmToTwip(1.5),
        ]);
        $section->addText('Хроника инцидентов по Электромеханической службе Дирекции инфраструктуры');
        $section->addText($this->periodString ?? '');

        $table = $section->addTable(['borderSize' => 1, 'cellMargin' => 50]);
        $table->addRow(null, ['tblHeader' => true]);
        $table->addCell(Converter::cmToTwip(2.29))->addText('Дата и время');
        $table->addCell(Converter::cmToTwip(2.46))->addText('ЭМЧ');
        $table->addCell(Converter::cmToTwip(6.5))->addText('Объект инфраструктуры');
        //$table->addCell(Converter::cmToTwip(3))->addText('Тип инцидента');
        $table->addCell(Converter::cmToTwip(6.75))->addText('Объект инцидента');
        $table->addCell(Converter::cmToTwip(3.75))->addText('Неисправность');
        $table->addCell(Converter::cmToTwip(3.25))->addText('Причина');
        $table->addCell(Converter::cmToTwip(2))->addText('Статус');
        foreach ($this->divisionsArray as $divisionItem) {
            $incidentRecordsByDivisionAndTypes = null;
            if ($this->collectionRecords->has($divisionItem)) {
                $incidentRecordsByDivisionAndTypes = $this->collectionRecords->get($divisionItem)->groupBy('incidentType.title');
            }
            $dropVoltagesRecordsByDivision = $this->dropVoltageCollectionRecords->get($divisionItem);
            if ($incidentRecordsByDivisionAndTypes || $dropVoltagesRecordsByDivision) {
                $table->addRow();
                $table->addCell(
                    Converter::cmToTwip(26.78),
                    [
                        'bgColor' => 'f2f2f2',
                        'gridSpan' => 7,
                        'valign' => 'top',
                    ]
                )
                    ->addText($divisionItem);
                if ($incidentRecordsByDivisionAndTypes && count($incidentRecordsByDivisionAndTypes) > 0) {
                    foreach ($incidentRecordsByDivisionAndTypes as $title => $recordsIncidentsForGroupingByType) {
                        $table->addRow();
                        $table->addCell(
                            Converter::cmToTwip(26.78),
                            [
                                'bgColor' => 'f2f2f2',
                                'gridSpan' => 7,
                                'valign' => 'top',
                            ]
                        )
                            ->addText($title);
                        foreach ($recordsIncidentsForGroupingByType as $record) {
                            $table->addRow();
                            $this->addDateColumnContent($record, $table);
                            $this->addDivisionColumnContent($record, $table);
                            $this->addObjectInfrastructureColumnContent($record, $table);
                            //$this->addTypeIncidentColumnContent($record, $table);
                            $this->addObjectIncidentColumnContent($record, $table);
                            $this->addIncidentDescriptionColumnContent($record, $table);
                            $this->addReasonIncidentColumnContent($record, $table);
                            $this->addStatusResolutionIncidentColumnContent($record, $table);
                            $this->addChroniclesInformationColumnContent($record, $table);
                        }

                    }
                }
                if ($dropVoltagesRecordsByDivision && count($dropVoltagesRecordsByDivision) > 0) {
                    $table->addRow();
                    $table->addCell(
                        Converter::cmToTwip(26.78),
                        [
                            'bgColor' => 'f2f2f2',
                            'gridSpan' => 7,
                            'valign' => 'top',
                        ]
                    )
                        ->addText('посадки напряжения');
                    foreach ($dropVoltagesRecordsByDivision as $dropVoltageRecordItem) {
                        $table->addRow();
                        $this->addDateDropVoltageColumnContent($dropVoltageRecordItem, $table);
                        $this->addDropVoltageDivisionColumnContent($dropVoltageRecordItem, $table);
                        $this->addDropVoltageLocationColumnContent($dropVoltageRecordItem, $table);
                        $this->addDropVoltageDescriptionColumnContent($dropVoltageRecordItem, $table);
                        $this->addDropVoltageStatusColumnContent($dropVoltageRecordItem, $table);
                        $this->addDropVoltageDevicesAndEventColumnContent($dropVoltageRecordItem, $table);
                    }
                }
            }
        }
        $this->addInspectionTableContent($section);
        $section->addTextBreak();
        $section->addText('Отчет подготовил ' . Auth::user()->position . ' ' . Auth::user()->name, ['size' => 10],
            ['alignment' => Jc::END]);
    }

    protected function addDropVoltageDevicesAndEventColumnContent(DropVoltage $record, Table $table): void
    {
        $table->addRow();
        $dropVoltageDevicesAndEventColumnContent = $table->addCell(Converter::cmToTwip(26.78),
            ['gridSpan' => 7, 'valign' => 'top']);
        if (($record->dropVoltageDevices?->count() ?? 0) > 0) {
            $textDropVoltageDevices = 'Отключились ИТУ: ';
            $listDropVoltageDevices = [];
            foreach ($record->dropVoltageDevices as $deviceItem) {
                $deviceItemText = $deviceItem->type . ' ' . $deviceItem->name;
                if ($deviceItem->comment) {
                    $deviceItemText .= ' (' . $deviceItem->comment . ')';
                }
                $listDropVoltageDevices[] = $deviceItemText;
            }
            $textDropVoltageDevices .= implode(', ', $listDropVoltageDevices);
            $dropVoltageDevicesAndEventColumnContent->addText($textDropVoltageDevices, $this->setFontStyleDropVoltage(),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(10),
                    'alignment' => Jc::START,
                ]);
        }
        if (($record->dropVoltageEventChronicles?->count() ?? 0) > 0) {
            $listEvents = [];
            foreach ($record->dropVoltageEventChronicles as $eventChronicleItem) {
                $listEvents[] = $eventChronicleItem->datetime_event->format('d/m H:i') . ' - ' . $eventChronicleItem->description;
            }
            $dropVoltageDevicesAndEventColumnContent->addText(implode('; ', $listEvents),
                $this->setFontStyleDropVoltage(),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(10),
                    'alignment' => Jc::START,
                ]);
        }
        $authorTextList = [];
        $authorTextList[] = $this->getPositionAndFioDispatcherFromUserstampsRecord($record->creator);
        $authorTextList[] = $this->getPositionAndFioDispatcherFromUserstampsRecord($record->editor);
        $dropVoltageDevicesAndEventColumnContent->addText(implode(', ', array_unique($authorTextList)),
            $this->setFontStyleDropVoltage(),
            [
                'spacingLineRule' => LineSpacingRule::EXACT,
                'spacing' => Converter::pointToTwip(10),
                'alignment' => Jc::END,
            ]);
    }

    protected function addDateColumnContent(Incident $record, Table $table): void
    {
        $dateColumn = $table->addCell(Converter::cmToTwip(2.29));
        $dateColumn->addText($record->datetime_incident->format('d.m.y'), $this->setFontStyleIncident($record));
        $dateColumn->addText($record->datetime_incident->format('H:i'), $this->setFontStyleIncident($record));
        if ($record->incident_classification === 'ННР') {
            $dateColumn->addText(
                $record->number_nnr ? $record->incident_classification . ' №' . $record->number_nnr : $record->incident_classification,
                [
                    'size' => 8,
                    'color' => 'ffffff',
                    'fgColor' => Font::FGCOLOR_BLACK,
                ],
                [
                    'spacing' => Converter::pointToTwip(10),
                    'spacingLineRule' => LineSpacingRule::EXACT,
                ]
            );
        } else {
            $dateColumn->addText(
                $record->incident_classification,
                $this->setFontStyleIncident($record, 8),
                [
                    'spacing' => Converter::pointToTwip(10),
                    'spacingLineRule' => LineSpacingRule::EXACT,
                ]);
        }
    }

    protected function addDateDropVoltageColumnContent(DropVoltage $record, Table $table): void
    {
        $dateColumn = $table->addCell(Converter::cmToTwip(2.29));
        $dateColumn->addText($record->datetime_drop->format('d.m.y'), $this->setFontStyleDropVoltage());
        $dateColumn->addText($record->datetime_drop->format('H:i'), $this->setFontStyleDropVoltage());

        $dateColumn->addText(
            'Посадка № ' . $record->id,
            $this->setFontStyleDropVoltage(8),
            [
                'spacing' => Converter::pointToTwip(10),
                'spacingLineRule' => LineSpacingRule::EXACT,
            ]);

    }

    protected function addDivisionColumnContent(Incident $record, Table $table): void
    {
        $table->addCell(Converter::cmToTwip(2.46))
            ->addText($record->division->short_name, $this->setFontStyleIncident($record));
    }

    protected function addDropVoltageDivisionColumnContent(DropVoltage $record, Table $table): void
    {
        $table->addCell(Converter::cmToTwip(2.46))
            ->addText($record->groupInfrastructureObject?->division?->short_name, $this->setFontStyleDropVoltage(12));
    }

    protected function addObjectInfrastructureColumnContent(Incident $record, Table $table): void
    {
        $objectInfrastructureColumn = $table->addCell(Converter::cmToTwip(6.5));
        $objectInfrastructureText = $record->objectInfrastructure->groupInfrastructureObject->short_title . '.';
        $objectInfrastructureText .= TypesInfrastructureObjectEnum::tryFrom($record->objectInfrastructure->type)->getLabel() . ' ';
        $objectInfrastructureText .= $record->objectInfrastructure->name;
        $objectInfrastructureColumn->addText($objectInfrastructureText, $this->setFontStyleIncident($record));
        if ($record->location) {
            $objectInfrastructureColumn->addText($record->location,
                $this->setFontStyleIncident($record, 10),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(12),
                ]);
        }
        if ($record->detail_location) {
            $objectInfrastructureColumn->addText($record->detail_location,
                $this->setFontStyleIncident($record, 10),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(12),
                ]);
        }

    }

    protected function addDropVoltageLocationColumnContent(DropVoltage $record, Table $table): void
    {
        $dropVoltageLocationColumnContent = $table->addCell(
            Converter::cmToTwip(6.75),
            [
                'gridSpan' => 2,
                'valign' => 'top',
            ]
        );
        $dropVoltageLocationColumnContent->addText($record->groupInfrastructureObject?->title,
            $this->setFontStyleDropVoltage(12));
        $dropVoltageLocationColumnContent->addText($record->detail_location, $this->setFontStyleDropVoltage(10));

    }

    protected function addDropVoltageDescriptionColumnContent(DropVoltage $record, Table $table): void
    {
        $table->addCell(
            Converter::cmToTwip(10.75),
            [
                'gridSpan' => 2,
                'valign' => 'top',
            ]
        )->addText($record->detail_drop, $this->setFontStyleDropVoltage());

    }

    protected function addTypeIncidentColumnContent(Incident $record, Table $table): void
    {
        $table->addCell(Converter::cmToTwip(3))->addText($record->incidentType->title,
            $this->setFontStyleIncident($record));
    }

    protected function addObjectIncidentColumnContent(Incident $record, Table $table): void
    {
        $objectIncidentColumn = $table->addCell(Converter::cmToTwip(6.75));
        $textItuDirectoryObject = '';
        if ($record->ituDirectoryObject?->exists()) {
            if ($record->ituDirectoryObject?->ituSpecie?->title) {
                $textItuDirectoryObject = $record->ituDirectoryObject?->ituSpecie?->title . ' ';
            }
            $textItuDirectoryObject .= $record->ituDirectoryObject?->title ?? '';
            $objectIncidentColumn->addText($textItuDirectoryObject, $this->setFontStyleIncident($record, 12, true));
        } else {
            if ($record->ituSpecie?->title) {
                $objectIncidentColumn->addText($record->ituSpecie?->title,
                    $this->setFontStyleIncident($record, 12, true));
            }
            if ($record->ituCharacteristic?->title) {
                $objectIncidentColumn->addText($record->ituCharacteristic?->title,
                    $this->setFontStyleIncident($record));
            }
        }

        if ($record->detail_object_incident) {
            $objectIncidentColumn->addText($record->detail_object_incident,
                $this->setFontStyleIncident($record, 10),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(10),
                ]);
        }
    }

    protected function addIncidentDescriptionColumnContent(Incident $record, Table $table): void
    {
        $incidentDescriptionColumn = $table->addCell(Converter::cmToTwip(3.75));
        if ($record->ituFault?->exists()) {
            $incidentDescriptionColumn->addText($record->ituFault?->title,
                $this->setFontStyleIncident($record, 12, true));
        }
        if ($record->ituElement?->exists()) {
            $incidentDescriptionColumn->addText($record->ituElement?->title, $this->setFontStyleIncident($record));
        }
        if ($record->detail_incident) {
            $incidentDescriptionColumn->addText($record->detail_incident,
                $this->setFontStyleIncident($record, 10),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(10),
                ]
            );
        }
    }

    protected function addReasonIncidentColumnContent(Incident $record, Table $table): void
    {
        $incidentReasonColumn = $table->addCell(Converter::cmToTwip(3.25));
        if ($record->has('dropVoltage') && $record->dropVoltage?->id) {
            $incidentReasonColumn->addText('Посадка № ' . $record->dropVoltage?->id,
                $this->setFontStyleIncident($record));
        }
        if ($record->ituReasonBreakdown?->exists()) {
            $incidentReasonColumn->addText($record->ituReasonBreakdown->title,
                $this->setFontStyleIncident($record));
        }
    }

    protected function addStatusResolutionIncidentColumnContent(Incident $record, Table $table): void
    {
        $statusResolutionColumn = $table->addCell(Converter::cmToTwip(2));
        try {
            $statusResolutionColumn->addText(
                IncidentStatuses::tryFrom($record->status_resolution)->getLabel(),
                $this->setFontStyleIncident($record, 10),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(10),
                ]
            );
            if ($record->status_resolution === IncidentStatuses::InWorking->value) {
                $statusResolutionColumn->addText($record->repair_date?->format('d.m.y') ?? '',
                    $this->setFontStyleIncident($record, 10));
                $statusResolutionColumn->addText($record->repair_date?->format('H:i') ?? '',
                    $this->setFontStyleIncident($record, 10));
            }
        } catch (Exception) {

        }
    }

    protected function addDropVoltageStatusColumnContent(DropVoltage $record, Table $table): void
    {
        $statusResolutionColumn = $table->addCell(Converter::cmToTwip(2));
        try {
            $statusResolutionColumn->addText(
                DropVoltageStatuses::tryFrom($record->status_drop)->getLabel(), $this->setFontStyleDropVoltage(),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(10),
                ]
            );
        } catch (Exception) {

        }
    }

    protected function addChroniclesInformationColumnContent(Incident $record, Table $table): void
    {
        $table->addRow();
        $chroniclesInformationColumn = $table->addCell(Converter::cmToTwip(26.78),
            ['gridSpan' => 7, 'valign' => 'top']);
        $informationTextList = [];
        if ($record->reported_by) {
            $informationTextList[] = 'Сообщил: ' . Str::of($record->reported_by)->trim()->finish('.');
        }
        if ($record->incidentEmployeeInformations?->count() > 0) {
            $informationTextList[] = 'Информированы:';
            foreach ($record->incidentEmployeeInformations as $incidentEmployeeInformation) {
                $informationTextList[] = $incidentEmployeeInformation->position .
                    ' ' .
                    $incidentEmployeeInformation->fio .
                    ' ' .
                    $incidentEmployeeInformation->information_time?->format('H:i');
            }
        }
        if ($record->incidentEmployeeReferrals?->count() > 0) {
            $informationTextList[] = 'Направлены:';
            foreach ($record->incidentEmployeeReferrals as $incidentEmployeeReferral) {
                $incidentEmployeeReferralText =
                    $incidentEmployeeReferral->position . ' ' .
                    $incidentEmployeeReferral->fio . ' ' .
                    $incidentEmployeeReferral->direction_time?->format('H:i');
                if ($incidentEmployeeReferral->arrival_time) {
                    $incidentEmployeeReferralText .= ' (прибыл ' . $incidentEmployeeReferral->arrival_time->format('H:i') . ')';
                }
                $informationTextList[] = $incidentEmployeeReferralText;

            }
        }
        $chroniclesInformationColumn->addText(implode(' ', $informationTextList),
            $this->setFontStyleIncident($record, 10),
            [
                'spacingLineRule' => LineSpacingRule::EXACT,
                'spacing' => Converter::pointToTwip(10),
                'alignment' => Jc::START,
            ]);
        $authorTextList = [];
        $authorTextList[] = $this->getPositionAndFioDispatcherFromUserstampsRecord($record->creator);
        $authorTextList[] = $this->getPositionAndFioDispatcherFromUserstampsRecord($record->editor);
        if ($record->eventChronicles?->count() > 0) {
            $eventChroniclesTextList = [];
            foreach ($record->eventChronicles as $event) {
                if (!$event->is_show_in_reports) {
                    continue;
                }
                $authorTextList[] = $this->getPositionAndFioDispatcherFromUserstampsRecord($event->creator);
                $authorTextList[] = $this->getPositionAndFioDispatcherFromUserstampsRecord($event->editor);
                $eventChroniclesTextList[] = $event->datetime_event->format('d/m H:i') . ' ' .
                    Str::of($event->description)->trim()->finish('.');
            }
            $chroniclesInformationColumn->addText(implode(' ', $eventChroniclesTextList),
                $this->setFontStyleIncident($record, 10),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(10),
                    'alignment' => Jc::START,
                ]);
        }
        if ($record->appropriate_measures) {
            $chroniclesInformationColumn->addText('Принятые меры: ' . $record->appropriate_measures,
                $this->setFontStyleIncident($record, 10),
                [
                    'spacingLineRule' => LineSpacingRule::EXACT,
                    'spacing' => Converter::pointToTwip(10),
                    'alignment' => Jc::START,
                ]);
        }
        $chroniclesInformationColumn->addText(implode(', ', array_unique($authorTextList)),
            $this->setFontStyleIncident($record, 10),
            [
                'spacingLineRule' => LineSpacingRule::EXACT,
                'spacing' => Converter::pointToTwip(10),
                'alignment' => Jc::END,
            ]);
    }

    protected function getPositionAndFioDispatcherFromUserstampsRecord(User $user): string
    {
        return $user->position . ' ' . $user->name;
    }

    protected function addInspectionTableContent(Section $currentSection): void
    {
        if ($this->startDate) {
            try {
                $inspections = Inspection::where(function (Builder $query) {
                    $query->whereIn('type', [
                        InspectionsTypes::DaySecurity,
                        InspectionsTypes::SurpriseInspection,
                    ]);
                    $query->whereDate('date_start', $this->carbonStartDate->toDateString());
                })
                    ->orWhere(function (Builder $query) {
                        $query->where('type', InspectionsTypes::NightInspection);
                        $query->whereDate('date_start', $this->carbonStartDate->addDay()->toDateString());
                    })
                    ->with([
                        'division',
                        'objectInfrastructureInspectionItems.objectInfrastructure',
                        'inspector'
                    ])
                    ->get()
                    ->sortBy('division.report_position');
                if ($inspections->count() > 0) {
                    $currentSection->addTextBreak();
                    $currentSection->addText('Проверки руководителей', null, ['alignment' => Jc::START]);
                    $tableInspection = $currentSection->addTable(['borderSize' => 1, 'cellMargin' => 50]);
                    $tableInspection->addRow(null, ['tblHeader' => true]);
                    $tableInspection->addCell(Converter::cmToTwip(3.5))->addText('Дата проверки');
                    $tableInspection->addCell(Converter::cmToTwip(4.75))->addText('Тип проверки');
                    $tableInspection->addCell(Converter::cmToTwip(2.75))->addText('ЭМЧ');
                    $tableInspection->addCell(Converter::cmToTwip(2.75))->addText('Должность');
                    $tableInspection->addCell(Converter::cmToTwip(4.25))->addText('ФИО');
                    $tableInspection->addCell(Converter::cmToTwip(8.78))->addText('Места проведения');
                    foreach ($inspections as $inspection) {
                        $tableInspection->addRow();
                        $dateText = $inspection->date_start->format('d.m');
                        if ($inspection->type === InspectionsTypes::NightInspection->value) {
                            $dateText = 'с ' . $inspection->date_start->subDay()->format('d.m') . ' по ' . $inspection->date_start->format('d.m');
                        }
                        $tableInspection->addCell()->addText($dateText);
                        $tableInspection->addCell()->addText(InspectionsTypes::tryFrom($inspection->type)->getLabel());
                        $tableInspection->addCell()->addText($inspection->division->short_name);
                        $tableInspection->addCell()->addText($inspection->position);
                        $tableInspection->addCell()->addText($inspection->inspector->short_name);
                        $cellObjectInf = $tableInspection->addCell();
                        if ($inspection->objectInfrastructures?->count() > 0) {
                            $inspection->objectInfrastructures->each(function (
                                ObjectInfrastructure $objectInfrastructure
                            ) use ($cellObjectInf) {
                                $cellObjectInf->addText(
                                    TypesInfrastructureObjectEnum::tryFrom($objectInfrastructure->type)->getLabel() . ' ' . $objectInfrastructure->name,
                                    [
                                        'size' => 10,
                                    ],
                                    [
                                        'spacingLineRule' => LineSpacingRule::EXACT,
                                        'spacing' => Converter::pointToTwip(10),
                                    ]);
                            });
                        }
                    }
                }

            } catch (Exception) {

            }
        }
    }

    protected function setFontStyleIncident(Incident $incident, ?int $fontSize = 12, bool $isUnderline = false): array
    {
        $fontStyle = [
            'size' => $fontSize,
            'color' => '7f7f7f',
            'italic' => true,
        ];
        if ($isUnderline) {
            $fontStyle['underline'] = Font::UNDERLINE_SINGLE;
        }
        //Текущие сутки ННР, АРМ
        if (($incident->incident_classification === 'ННР' ||
            $incident->incident_classification === 'АРМ') &&
            $incident->datetime_incident->between($this->carbonStartDate, $this->carbonEndDate)
        ) {
            $fontStyle['color'] = '000000';
            $fontStyle['italic'] = 'false';
            $fontStyle['bold'] = true;
        }
        if ($incident->incident_classification === 'ННР' && $incident->status_resolution === IncidentStatuses::InWorking->value) {
            $fontStyle['italic'] = 'false';
        }
        //Если ННР и устранен
        return $fontStyle;
    }

    protected function setFontStyleDropVoltage(?int $fontSize = 10): array
    {
        return [
            'size' => $fontSize,
            'color' => '808080',
            'italic' => false,
        ];
    }
}
