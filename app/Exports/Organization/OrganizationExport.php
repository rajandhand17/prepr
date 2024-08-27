<?php

namespace App\Exports\Organization;

use App\Exports\Organization\Components\OrganizationComponentsExport;
use App\Exports\Organization\Components\OrganizationDetailExport;
use App\Exports\Organization\Components\OrganizationMembersExport;
use App\Models\Organization;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrganizationExport implements WithMultipleSheets
{
    private $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Returns an array of sheets to be included in the export.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            new OrganizationDetailExport($this->organization),
            new OrganizationComponentsExport($this->organization),
            new OrganizationMembersExport($this->organization),
        ];
    }
}
