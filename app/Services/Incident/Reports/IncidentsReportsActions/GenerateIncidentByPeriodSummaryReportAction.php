<?php

namespace App\Services\Incident\Reports\IncidentsReportsActions;


use App\Services\Incident\Reports\CustomizableSummaryIncidentReportService;
use App\Services\Incident\Reports\IncidentsReportsConfigEnum\TypeIncidentsStrategy;
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

class GenerateIncidentByPeriodSummaryReportAction extends Action
{

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'generateIncidentByPeriodSummaryReportAction')
            ->label('Инциденты за период')
            ->form([
                Grid::make(4)->schema([
                    DateRangePicker::make('date_range')
                        ->label('Период')
                        ->hintActions([
                            \Filament\Forms\Components\Actions\Action::make('set_7h_date_incident_filter')
                                ->label('07:00')
                                ->action(function (Set $set) {
                                    $set('time_start_day_report_incidents', Carbon::now()->setTime(7, 0)->toDateTimeString());
                                    $set('time_end_day_report_incidents', Carbon::now()->setTime(7, 0)->toDateTimeString());
                                }),
                            \Filament\Forms\Components\Actions\Action::make('set_8h_date_incident_filter')
                                ->label('08:00')
                                ->action(function (Set $set) {
                                    $set('time_start_day_report_incidents', Carbon::now()->setTime(8, 0)->toDateTimeString());
                                    $set('time_end_day_report_incidents', Carbon::now()->setTime(8, 0)->toDateTimeString());
                                }),
                        ])
                        ->autoApply()
                        ->required()
                        ->rules([
                            'regex:/^\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}$/',
                        ])
                        ->validationMessages([
                            'regex' => 'Период должен быть в формате ДД/ММ/ГГГГ - ДД/ММ/ГГГГ (например, 06/01/2026 - 11/01/2026).',
                        ])
                        ->live(),
                    TimePicker::make('time_start_day_report_incidents')
                        ->label('Время от:')
                        ->native(false)
                        ->seconds(false)
                        ->default('07:00')
                        ->datalist([
                            '07:00',
                            '08:00',
                        ])
                        ->format('Y-m-d H:i:s') // Формат: 2026-01-18 11:00:00
                        ->rules([
                            'date_format:Y-m-d H:i:s',
                        ])
                        ->validationMessages([
                            'date_format' => 'Дата и время должны быть в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС (например, 2026-01-18 11:00:00).',
                        ])
                        ->live(),
                    TimePicker::make('time_end_day_report_incidents')
                        ->label('Время до:')
                        ->native(false)
                        ->seconds(false)
                        ->default('07:00')
                        ->datalist([
                            '07:00',
                            '08:00',
                        ])
                        ->format('Y-m-d H:i:s') // Формат: 2026-01-18 11:00:00
                        ->rules([
                            'date_format:Y-m-d H:i:s',
                        ])
                        ->validationMessages([
                            'date_format' => 'Дата и время должны быть в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС (например, 2026-01-18 11:00:00).',
                        ])
                        ->live(),
                    Radio::make('status')
                        ->label('Тип инцидентов')
                        ->options(collect(TypeIncidentsStrategy::cases())->mapWithKeys(function ($case) {
                            return [$case->value => $case->getLabel()];
                        })->all())
                        ->default(TypeIncidentsStrategy::AllIncidents->value)
                        ->required()
                        ->rule(function () {
                            return function ($attribute, $value, $fail) {
                                $validValues = collect(TypeIncidentsStrategy::cases())->pluck('value')->all();
                                if (!in_array($value, $validValues)) {
                                    $fail('Статус должен быть одним из допустимых значений.');
                                }
                            };
                        })
                        ->validationMessages([
                            'rule' => 'Выберите корректное значение статуса.',
                        ]),
                    Placeholder::make('comment')->hiddenLabel()
                        ->content(function (callable $get) {
                            return static::getCommentPlaceholderValue(
                                $get('date_range'),
                                $get('time_start_day_report_incidents'),
                                $get('time_end_day_report_incidents')
                            );
                        })
                        ->columnSpan(4)
                ]),
            ])
            ->action(function (array $data, CustomizableSummaryIncidentReportService $reportService) {
                try {
                    //Create DatePeriodService from Filament form fields
                    try {
                        $datePeriodService = new DatePeriodService();
                        $datePeriodService->setFromFilamentRangeAndTimeFields(
                            $data['date_range'],
                            $data['time_start_day_report_incidents'],
                            $data['time_end_day_report_incidents']
                        );
                    } catch (Exception $e) {
                        throw new Exception('Ошибка при обработке даты и времени: ' . $e->getMessage());
                    }
                    $reportStrategy = TypeIncidentsStrategy::from($data['status'])->getTypeSummaryReportStrategy();
                    $filename = $reportService->makeAndGetReportFilename($datePeriodService, $reportStrategy);
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

    protected static function getCommentPlaceholderValue(?string $dateRange, ?string $timeStart, ?string $timeEnd): string
    {
        try {
            // Проверяем, что все входные параметры заданы
            if (!$dateRange || !$timeStart || !$timeEnd) {
                return '';
            }

            // Разбиваем диапазон дат на начало и конец
            [$startDate, $endDate] = explode(' - ', $dateRange);

            // Форматируем дату и время начала
            $startDateTime = Carbon::createFromFormat('d/m/Y H:i:s', $startDate . ' ' . Carbon::parse($timeStart)->format('H:i:s'));
            $startFormatted = $startDateTime->format('d.m.Y H:i');

            // Форматируем дату и время окончания
            $endDateTime = Carbon::createFromFormat('d/m/Y H:i:s', $endDate . ' ' . Carbon::parse($timeEnd)->format('H:i:s'));
            $endFormatted = $endDateTime->format('d.m.Y H:i');

            // Возвращаем строку с периодом
            return "Период начала: {$startFormatted}, окончания: {$endFormatted}";
        } catch (Exception $e) {
            // В случае ошибки возвращаем пустую строку
            return '';
        }
    }
}
