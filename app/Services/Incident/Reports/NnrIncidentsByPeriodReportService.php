<?php

namespace App\Services\Incident\Reports;

use App\Models\Directories\Division;
use App\Models\Incident\Incident;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use Str;

class NnrIncidentsByPeriodReportService extends SummaryIncidentReportService
{
    public function makeAndGetReportFilename(
        ?string $periodString,
        ?array $recordsIds,
        ?Carbon $carbonStartDate,
        ?Carbon $carbonEndDate,
    ): ?string {
        try {
            $this->collectionRecords = Collection::make(Incident::incidentsForNnrReport(
                $carbonStartDate,
                $carbonEndDate
            )->get())
                ->groupBy('division.name');
            $this->startDate = $carbonStartDate;
            $this->carbonStartDate = $carbonStartDate;
            $this->carbonEndDate = $carbonEndDate;
            $this->periodString = Str::of('период: с ')
                ->append(
                    $this->carbonStartDate->format('d.m.Y H:i'),
                    ' по ',
                    $this->carbonEndDate->format('d.m.Y H:i')
                )
                ->toString();
            $this->divisionsArray = Division::orderBy('report_position')
                ->pluck('name')
                ->toArray();
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
            return null;
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
        $section->addText('Хроника номерных инцидентов по Электромеханической службе Дирекции инфраструктуры');
        $section->addText($this->periodString ?? '');

        $table = $section->addTable(['borderSize' => 1, 'cellMargin' => 50]);
        $table->addRow(null, ['tblHeader' => true]);
        $table->addCell(Converter::cmToTwip(2.29))->addText('Дата и время');
        $table->addCell(Converter::cmToTwip(2.46))->addText('ЭМЧ');
        $table->addCell(Converter::cmToTwip(6.5))->addText('Объект инфраструктуры');
        $table->addCell(Converter::cmToTwip(6.75))->addText('Объект инцидента');
        $table->addCell(Converter::cmToTwip(3.75))->addText('Неисправность');
        $table->addCell(Converter::cmToTwip(3.25))->addText('Причина');
        $table->addCell(Converter::cmToTwip(2))->addText('Статус');
        foreach ($this->divisionsArray as $divisionItem) {
            $incidentRecordsByDivisionAndTypes = null;
            if ($this->collectionRecords->has($divisionItem)) {
                $incidentRecordsByDivisionAndTypes = $this->collectionRecords->get($divisionItem)->groupBy('incidentType.title');
            }
            if ($incidentRecordsByDivisionAndTypes) {
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
                if (count($incidentRecordsByDivisionAndTypes) > 0) {
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
                            $this->addObjectIncidentColumnContent($record, $table);
                            $this->addIncidentDescriptionColumnContent($record, $table);
                            $this->addReasonIncidentColumnContent($record, $table);
                            $this->addStatusResolutionIncidentColumnContent($record, $table);
                            $this->addChroniclesInformationColumnContent($record, $table);
                        }
                    }
                }
            }
        }
        $section->addTextBreak();
        $section->addText('Отчет подготовил ' . Auth::user()->position . ' ' . Auth::user()->name, ['size' => 10],
            ['alignment' => Jc::END]);
    }
}
