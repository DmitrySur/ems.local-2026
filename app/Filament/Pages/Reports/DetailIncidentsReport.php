<?php

namespace App\Filament\Pages\Reports;

use App\Exports\IncidentsExport;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Str;

class DetailIncidentsReport extends Page
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.reports.detail-incidents-report';
    protected static ?string $navigationGroup = 'Отчеты';
    protected static ?string $title = 'Список инцидентов';

    public function getDetailIncidentsReportAction()
    {
        return Action::make('getDetailIncidentsReportAction')
            ->label('Получить подробный список инцидентов за период:')
            ->form([
                DateRangePicker::make('period')
                    ->label('Период')
                    ->required()
            ])
            ->action(function (array $data) {
                $dateArrayPeriod = $this->parseDatePeriod($data['period']);
                if ($dateArrayPeriod) {
                    return Excel::download(new IncidentsExport($dateArrayPeriod), 'инциденты.xlsx');
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
            try {
                $datePeriodArray['startDate'] = Carbon::createFromFormat('d/m/Y',
                    $startDate)->setHour(0)->setMinute(0)->setSecond(0)->setMicro(0);
                $datePeriodArray['endDate'] = Carbon::createFromFormat('d/m/Y',
                    $endDate)->setHour(24)->setMinute(0)->setSecond(0)->setMicro(0);
                return $datePeriodArray;
            } catch (Exception) {
                return false;
            }
        }
        return false;
    }
}
