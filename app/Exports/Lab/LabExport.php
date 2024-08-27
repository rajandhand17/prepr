<?php

namespace App\Exports\Lab;

use App\Exports\Lab\Components\LabComponentsExport;
use App\Exports\Lab\Components\LabDetailExport;
use App\Exports\Lab\Components\LabMembersExport;
use App\Models\Lab;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LabExport implements WithMultipleSheets
{
    private $lab;

    public function __construct(Lab $lab)
    {
        $this->lab = $lab;
    }

    /**
     * Returns an array of sheets to be included in the export.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            new LabDetailExport($this->lab),
            new LabComponentsExport($this->lab),
            new LabMembersExport($this->lab),
        ];
    }
}
