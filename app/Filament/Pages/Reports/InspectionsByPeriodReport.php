<?php

namespace App\Filament\Pages\Reports;

use App\Exports\IncidentsExport;
use App\Exports\InspectionsByPeriodExport;
use App\Services\Incident\Reports\InspectionsByPeriodReportService;
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

class InspectionsByPeriodReport extends Page
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.reports.detail-incidents-report';
    protected static ?string $navigationGroup = 'Отчеты';
    protected static ?string $title = 'Проверки за период';

    public function getDetailIncidentsReportAction()
    {
        return Action::make('getDetailIncidentsReportAction')
            ->label('Получить проверки за период:')
            ->form([
                DateRangePicker::make('period')
                    ->label('Период дат')
                    ->autoApply()
                    ->required(),
            ])
            ->action(function (array $data, InspectionsByPeriodReportService $reportService) {
                $dateArrayPeriod = null;
                try {
                    $dateArrayPeriod = $this->parseDatePeriod($data['period']);
                } catch (Exception $exception) {
                    Notification::make()
                        ->title('Ошибка преобразования входных параметров')
                        ->icon('heroicon-c-minus-circle')
                        ->iconColor('danger')
                        ->send();
                }
                if ($dateArrayPeriod) {
                    return Excel::download($reportService->createAndGetReport(
                        $dateArrayPeriod['startDate'],
                        $dateArrayPeriod['endDate']), 'Проверки руководителей.xlsx');
                }
                return null;
            });
    }

    protected function parseDatePeriod(string $textDatePeriod): bool|array
    {
        if (!empty($textDatePeriod)) {
            $startDate = Str::before($textDatePeriod, ' - ');
            $endDate = Str::after($textDatePeriod, ' - ');
            $datePeriodArray = [];
            $datePeriodArray['startDate'] = Carbon::createFromFormat('d/m/Y', $startDate)
                ->setHour(0)->setMinute(0)->setSecond(0)->setMicro(0);
            $datePeriodArray['endDate'] = Carbon::createFromFormat('d/m/Y', $endDate)
                ->setHour(23)->setMinute(59)->setSecond(59)->setMicro(0);;
            return $datePeriodArray;
        }
        return false;
    }
}
