<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Resources\Incident\IncidentResource;
use App\Models\Incident\Incident;
use App\Services\Incident\Reports\SummaryIncidentReportService;
use Auth;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class DailyIncidents extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Отчеты';

    protected static string $view = 'filament.pages.reports.daily-incidents';

    protected static ?string $title = 'Инциденты за сутки';

    public static function canAccess(): bool
    {
        if (Auth::user()->hasRole('admin')) {
            return true;
        }
        if (Auth::user()->hasPermissionTo('incident_get_daily_pivot_report')) {
            return true;
        }

        return false;
    }

    public string|Carbon|null $startDate;

    public string|Carbon|null $endDate;

    public string|HtmlString|null $periodReportDescription = '';

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        $tableColumns = IncidentResource::table($table)->getColumns();

        return $table->query((Incident::query()))
            ->columns($tableColumns)
            ->description('')
            ->actions([])
            ->filters([
                Filter::make('datetime_incident_filter')
                    ->default()
                    ->form([
                        DatePicker::make('date_incident_filter')
                            ->native(false)
                            ->label('Истекшие сутки:')
                            ->displayFormat('d.m.Y')
                            ->placeholder('Выберите отчетную дату')
                            ->closeOnDateSelection()
                            ->required()
                            ->default(now())
                            ->columnSpan(2)
                            ->hintActions([
                                Action::make('set_7h_date_incident_filter')
                                    ->label('07:00')
                                    ->action(function (Set $set) {
                                        $set('start_time_incident_filter', Carbon::now()->setTime(7, 0));
                                        $set('end_time_incident_filter', Carbon::now()->setTime(7, 0));
                                    }),
                                Action::make('set_8h_date_incident_filter')
                                    ->label('08:00')
                                    ->action(function (Set $set) {
                                        $set('start_time_incident_filter', Carbon::now()->setTime(8, 0));
                                        $set('end_time_incident_filter', Carbon::now()->setTime(8, 0));
                                    }),
                            ])
                            ->live(),
                        TimePicker::make('start_time_incident_filter')
                            ->label('Время от:')
                            ->native(false)
                            ->seconds(false)
                            ->default('07:00')
                            ->datalist([
                                '07:00',
                                '08:00',
                            ])
                            ->live(),
                        TimePicker::make('end_time_incident_filter')
                            ->label('Время до:')
                            ->native(false)
                            ->seconds(false)
                            ->default('07:00')
                            ->datalist([
                                '07:00',
                                '08:00',
                            ])
                            ->live(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (
                            $data['date_incident_filter'] &&
                            $data['start_time_incident_filter'] &&
                            $data['end_time_incident_filter']
                        ) {
                            try {
                                self::setPeriodReportDescription($data['date_incident_filter'],
                                    $data['start_time_incident_filter'], $data['end_time_incident_filter']);
                                $this->subheading = $this->periodReportDescription;

                                $date = Carbon::parse($data['date_incident_filter']);
                                $startTime = Carbon::parse($data['start_time_incident_filter']);
                                $endTime = Carbon::parse($data['end_time_incident_filter']);

                                $startDateFilter = Carbon::parse($date->toDateString())
                                    ->subDays()
                                    ->hour($startTime->hour)
                                    ->minute($startTime->minute);
                                $endDateFilter = $date
                                    ->hour($endTime->hour)
                                    ->minute($endTime->minute);
                                //Даты отчета
                                $this->startDate = $startDateFilter;
                                $this->endDate = $endDateFilter;

                                return $query->whereBetween('datetime_incident',
                                    [
                                        $startDateFilter->toDateTimeString(),
                                        $endDateFilter->toDateTimeString(),
                                    ]);
                            } catch (Exception) {
                                return $query;
                            }
                        }

                        return $query;
                    })
                    ->columns(4),
                Filter::make('is_number_incident_filter')
                    ->form([
                        ToggleButtons::make('is_number_incident_filter_data')
                            ->label('Отображать инциденты')
                            ->options([
                                'number_nnr' => 'Только номерные ННР',
                                'all' => 'Все',
                            ])
                            ->colors([
                                'number_nnr' => 'success',
                                'all' => 'primary',
                            ])
                            ->inline()
                            ->default('all'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['is_number_incident_filter_data'] === 'number_nnr') {
                            return $query
                                ->where('incident_classification', '=', 'ННР')
                                ->whereNotNull('number_nnr');
                        }

                        return $query;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(5)
            ->filtersFormSchema(fn(array $filters): array => [
                Grid::make('3')
                    ->schema([
                        $filters['datetime_incident_filter'],
                        $filters['is_number_incident_filter'],
                    ])
                    ->columnSpanFull(),
            ])
            ->deferFilters()
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'incidentType',
                        'ituSpecie',
                        'division',
                        'ituCharacteristic',
                        'ituDirectoryObject.ituSpecie',
                        'ituElement',
                        'ituFault',
                        'ituReasonBreakdown',
                        'objectInfrastructure',
                        'eventChronicles.creator',
                        'eventChronicles.editor',
                        'incidentEmployeeReferrals',
                        'incidentEmployeeInformations',
                        'dropVoltage',
                        'creator',
                        'editor',
                    ])
                    ->leftJoin('divisions', 'incidents.division_id', '=', 'divisions.id')
                    ->leftJoin('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
                    ->select('incidents.*')
                    ->orderBy('incident_types.report_position')
                    ->orderBy('divisions.report_position')
                    ->orderBy('datetime_incident');
            })
            ->defaultPaginationPageOption('all')
            ->deselectAllRecordsWhenFiltered()
            ->bulkActions([
                BulkAction::make('get_summary_incident_report')
                    ->label('Выгрузить отчет')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (
                        Collection $records,
                        SummaryIncidentReportService $reportService
                    ) {
                        $recordsIds = $records->pluck('id')->toArray();
                        if (count($recordsIds) > 0) {
                            $filename = $reportService->makeAndGetReportFilename(
                                $this->periodReportDescription,
                                $recordsIds,
                                $this->startDate,
                                $this->endDate);
                            if ($filename && file_exists($filename)) {
                                return response()->download($filename)->deleteFileAfterSend();
                            }
                        }
                        return null;
                    }),
            ])
            ->paginated(false);
    }

    protected function setPeriodReportDescription(
        $dateIncidentFilter,
        $startTimeIncidentFilter,
        $endTimeIncidentFilter
    ): void {
        try {
            $date = Carbon::parse($dateIncidentFilter);
            $startTime = Carbon::parse($startTimeIncidentFilter);
            $endTime = Carbon::parse($endTimeIncidentFilter);
            $startDateFilter = Carbon::parse($date->toDateString())
                ->subDays()
                ->hour($startTime->hour)
                ->minute($startTime->minute);
            $endDateFilter = $date
                ->hour($endTime->hour)
                ->minute($endTime->minute);
            $this->periodReportDescription = 'период отчета с ' . $startDateFilter->format('d.m.Y H:i') . ' по ' . $endDateFilter->format('d.m.Y H:i');

        } catch (Exception) {

        }
    }
}
