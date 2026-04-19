<?php

namespace App\Services\Incident\Reports;

use App\Enum\InspectionsTypes;
use App\Exports\InspectionsByPeriodExport;
use App\Models\Inspection\Inspection;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\DB;

class InspectionsByPeriodReportService
{
    public function createAndGetReport(
        ?Carbon $carbonStartDate,
        ?Carbon $carbonEndDate,
    ): InspectionsByPeriodExport {
        $inspectionListFromDb = DB::table('inspections as inc')
            ->select([
                'inc.id',
                'inc.date_start',
                'inc.type',
                'inc.start_time',
                'inc.end_time',
                'inc.position',
                'inc.subdivisions',
                'divisions.short_name as division_short_name',
                'divisions.report_position',
                'inspectors.short_name',
                DB::raw("concat(divisions.short_name, ' ', inc.position, ' ', inspectors.short_name)  as GroupItem")
            ])
            ->leftJoin('divisions', 'inc.division_id', '=', 'divisions.id')
            ->leftJoin('inspectors', 'inc.inspector_id', '=', 'inspectors.id')
            ->orderBy('divisions.report_position')
            ->orderBy('inc.position')
            ->orderBy('inc.date_start')
            ->whereBetween('inc.date_start', [$carbonStartDate, $carbonEndDate])
            ->whereNull('inc.deleted_by')
            ->get();
        $inspectionCounter = DB::table('inspections')
            ->select('type', DB::raw("count(type) as countType"))
            ->whereBetween('date_start', [$carbonStartDate, $carbonEndDate])
            ->groupBy('type')
            ->pluck('countType', 'type')
            ->toArray();
        $inspectionListFromDbArray = collect($inspectionListFromDb)
            ->map(function ($x) {
                return (array)$x;
            })->toArray();
        $inspectionListFromDbArrayCollection = collect($inspectionListFromDbArray);
        $uniqueInspector = $inspectionListFromDbArrayCollection->unique('GroupItem')->groupBy('GroupItem')->toArray();
        $inspectionListGroupArray = $inspectionListFromDbArrayCollection
            ->groupBy(['GroupItem', 'type'])
            ->toArray();
        foreach ($inspectionListGroupArray as $keyGroup => $groupItems) {
            $nightInspectionCount = 0;
            $daySecurityCount = 0;
            $surpriseInspectionCount = 0;
            if (array_key_exists(InspectionsTypes::NightInspection->value,
                    $groupItems) && is_array($groupItems[InspectionsTypes::NightInspection->value])) {
                $nightInspectionCount = count($groupItems[InspectionsTypes::NightInspection->value]);
            }
            if (array_key_exists(InspectionsTypes::DaySecurity->value,
                    $groupItems) && is_array($groupItems[InspectionsTypes::DaySecurity->value])) {
                $daySecurityCount = count($groupItems[InspectionsTypes::DaySecurity->value]);
            }
            if (array_key_exists(InspectionsTypes::SurpriseInspection->value,
                    $groupItems) && is_array($groupItems[InspectionsTypes::SurpriseInspection->value])) {
                $surpriseInspectionCount = count($groupItems[InspectionsTypes::SurpriseInspection->value]);
            }
            $maxCountInspections = max($nightInspectionCount, $daySecurityCount, $surpriseInspectionCount);
            $inspectionListGroupArray[$keyGroup]['maxCount'] = $maxCountInspections;
        }
        $dateString = 'c ' . $carbonStartDate->format('d.m.Y') . ' по ' . $carbonStartDate->format('d.m.Y');
        return new InspectionsByPeriodExport($inspectionListGroupArray, $uniqueInspector, $dateString,
            $inspectionCounter);
    }
}
