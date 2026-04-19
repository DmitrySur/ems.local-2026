<x-filament-panels::page>
    <x-filament::card>
        <div class="flex flex-wrap gap-3">
            {{ $this->generateIncidentSummaryReportAction }}
            {{ $this->generateIncidentByPeriodSummaryReportAction }}
            {{ $this->generateIncidentInRepairReportAction }}
        </div>
    </x-filament::card>
</x-filament-panels::page>
