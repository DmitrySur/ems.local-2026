<?php

namespace App\Filament\Pages\Reports;

use App\Services\Incident\Reports\IncidentsReportsActions\GenerateIncidentByPeriodSummaryReportAction;
use App\Services\Incident\Reports\IncidentsReportsActions\GenerateIncidentInRepairReportAction;
use App\Services\Incident\Reports\IncidentsReportsActions\GenerateIncidentSummaryReportAction;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class GroupIncidentsReportsPage extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.reports.group-incidents-reports-page';
    // Настройки меню
    protected static ?string $navigationLabel = 'Отчеты по инцидентам за период';
    protected static ?string $title = 'Отчеты по инцидентам за период';
    protected static ?string $slug = 'group-incidents-reports-page';
    protected static ?string $navigationGroup = 'Отчеты';

    public function mount(): void
    {
        $this->mountedActionsData = [
            [
                'date_range' => '',
            ],
        ];
    }

    public function generateIncidentSummaryReportAction(): Action
    {
        return GenerateIncidentSummaryReportAction::make();
    }

    public function generateIncidentByPeriodSummaryReportAction(): Action
    {
        return GenerateIncidentByPeriodSummaryReportAction::make();
    }

    public function generateIncidentInRepairReportAction(): Action
    {
        return GenerateIncidentInRepairReportAction::make();
    }

    public static function canAccess(): bool
    {
        if (Auth::user()->hasRole('admin')) {
            return true;
        }
        if (Auth::user()->hasPermissionTo('group_incidents_reports_page')) {
            return true;
        }

        return false;
    }
}
