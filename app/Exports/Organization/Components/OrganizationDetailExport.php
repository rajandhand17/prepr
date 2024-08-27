<?php

namespace App\Exports\Organization\Components;

use App\Models\Organization;
use App\Services\Manage\Report\OrganizationReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class OrganizationDetailExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle
{
    private $organization;
    protected $organizationReportService;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
        $this->organizationReportService = new OrganizationReportService();
    }

    public function title(): string
    {
        return 'Organization Details';
    }

    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return collect($this->organizationReportService->getDetailExportData($this->organization));
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
