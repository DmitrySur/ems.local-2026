<?php

namespace App\Filament\Pages\Reports;

use App\Exports\IncidentsExport;
use App\Services\Incident\Reports\NnrIncidentsByPeriodReportService;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Str;

class NnrIncidentsByPeriodReport extends Page
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.reports.detail-incidents-report';
    protected static ?string $navigationGroup = 'Отчеты';
    protected static ?string $title = 'Инциденты номерные ННР';

    public function getDetailIncidentsReportAction()
    {
        return Action::make('getDetailIncidentsReportAction')
            ->label('Получить сводку по номерным ННР инцидентам за период:')
            ->form([
                DateRangePicker::make('period')
                    ->label('Период дат')
                    ->autoApply()
                    ->required(),
                Grid::make()
                    ->label('Фильтр времени')
                    ->schema([
                        TimePicker::make('start_time')
                            ->label('Время с:')
                            ->native(false)
                            ->seconds(false)
                            ->default('00:00')
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('Время по:')
                            ->native(false)
                            ->seconds(false)
                            ->default('23:59')
                            ->required()
                    ])
            ])
            ->action(function (array $data, NnrIncidentsByPeriodReportService $reportService) {
                $dateArrayPeriod = null;
                try {
                    $dateArrayPeriod = $this->parseDatePeriod($data['period'], $data['start_time'], $data['end_time']);
                } catch (Exception $exception) {
                    Notification::make()
                        ->title('Ошибка преобразования входных параметров')
                        ->icon('heroicon-c-minus-circle')
                        ->iconColor('danger')
                        ->send();
                }
                if ($dateArrayPeriod) {
                    $filename = $reportService->makeAndGetReportFilename(
                        '',
                        null,
                        $dateArrayPeriod['startDate'],
                        $dateArrayPeriod['endDate']);
                    if ($filename && file_exists($filename)) {
                        return response()->download($filename)->deleteFileAfterSend();
                    }
                    return null;
                }
                return null;
            });
    }

    protected function parseDatePeriod(string $textDatePeriod, string $startTime, string $endTime): bool|array
    {
        if (!empty($textDatePeriod)) {
            $startDate = Str::before($textDatePeriod, ' - ');
            $endDate = Str::after($textDatePeriod, ' - ');
            $datePeriodArray = [];
            $datePeriodArray['startDate'] = Carbon::createFromFormat('d/m/Y H:i',
                $startDate . ' ' . $startTime)->setSecond(0)->setMicro(0);
            $datePeriodArray['endDate'] = Carbon::createFromFormat('d/m/Y H:i',
                $endDate . ' ' . $endTime)->setSecond(0)->setMicro(0);
            return $datePeriodArray;
        }
        return false;
    }
}
