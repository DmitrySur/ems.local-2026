<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;

class InspectionsByPeriodExport implements FromView, WithDefaultStyles
{
    protected array $listInspectionArray = [];
    protected array $uniqueInspector = [];
    protected string $period = '';
    protected array $inspectorCounter = [];

    public function __construct(array $listInspectionArray, array $uniqueInspector, string $period, array $inspectorCounter)
    {
        $this->listInspectionArray = $listInspectionArray;
        $this->uniqueInspector = $uniqueInspector;
        $this->period = $period;
        $this->inspectorCounter = $inspectorCounter;
    }

    public function view(): View
    {
        return \view('reports.InspectionsByPeriodReportsViews.InspectionsByPeriodReportsMainView')
            ->with('listInspectionArray', $this->listInspectionArray)
            ->with('uniqueInspector', $this->uniqueInspector)
            ->with('datePeriod', $this->period)
            ->with('inspectorCounter', $this->inspectorCounter);
    }

    public function defaultStyles(Style $defaultStyle): array
    {
        return [
            'font' => [
                'name' => 'Times New Roman',
                'size' => 12
            ]
        ];
    }
}
