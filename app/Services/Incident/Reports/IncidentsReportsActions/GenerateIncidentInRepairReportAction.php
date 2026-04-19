<?php

namespace App\Services\Incident\Reports\IncidentsReportsActions;


use App\Services\Incident\Reports\CustomizableSummaryIncidentReportService;
use App\Services\Incident\Reports\IncidentsReportsConfigEnum\TypeIncidentsStrategy;
use App\Services\Incident\Reports\IncidentsReportsConfigEnum\TypeSummaryReportStrategy;
use App\Services\Incident\Reports\SummaryIncidentReportService;
use App\Services\Incident\Reports\UtilsReports\DatePeriodService;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class GenerateIncidentInRepairReportAction extends Action
{

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'generateIncidentInRepairReportAction')
            ->label('Журнал ремонта')
            ->action(function (CustomizableSummaryIncidentReportService $reportService) {
                try {
                    $reportStrategy = TypeSummaryReportStrategy::IncidentsInRepair;
                    $filename = $reportService->makeAndGetReportFilename(null, $reportStrategy);
                    if ($filename && file_exists($filename)) {
                        return response()->download($filename)->deleteFileAfterSend();
                    }


                    Notification::make()
                        ->title('Успех')
                        ->body('Данные успешно обработаны!')
                        ->success()
                        ->send();
                } catch (Exception $e) {
                    Notification::make()
                        ->title('Ошибка при формировании отчета')
                        ->body('Ошибка: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
                return null;
            })
            ->modalWidth('5xl')
            ->extraAttributes(['class' => 'whitespace-nowrap px-4 py-2 bg-primary-600 text-white rounded-lg']);

    }
}
