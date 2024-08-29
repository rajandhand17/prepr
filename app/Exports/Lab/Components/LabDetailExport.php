<?php

namespace App\Exports\Lab\Components;

use App\Models\Lab;
use App\Services\Manage\Report\LabReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class LabDetailExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle
{
    private $lab;
    protected $labReportService;

    public function __construct(Lab $lab)
    {
        $this->lab = $lab;
        $this->labReportService = new LabReportService();
    }

    public function title(): string
    {
        return 'Lab Details';
    }

    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return collect($this->labReportService->getDetailExportData($this->lab));
    }

    /**
     * @return int[]
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 40,
            'C' => 40,
        ];
    }
}
