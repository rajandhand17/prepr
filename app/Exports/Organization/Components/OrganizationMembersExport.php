<?php

namespace App\Exports\Organization\Components;

use App\Models\Organization;
use App\Services\Manage\Report\OrganizationReportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class OrganizationMembersExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle, WithHeadings, WithMapping
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
        return 'Organization Members';
    }

    public function headings(): array
    {
        return [
            'Name', 'User Name', 'Role', 'Email', 'Invitation Status', 'Account Activity', 'Learning Points', 'Learning Rank(in Organization)', 'Achievement', 'Completed Challenges', 'Completed Challenge Paths', 'Completed Labs', 'Completed Lab Programs', 'Completed Resource', 'Completed Resource Collections', 'Completed Resource Groups',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return collect(data_get($this->organizationReportService->getMembersWithModuleCompletion($this->organization, false), 'list'));
    }

    public function map($row): array
    {
        $user = $row->user;

        return [
            data_get($user,'full_name'),
            data_get($user,'username'),
            data_get($row,'role'),
            data_get($user,'email', data_get($row,'email')),
            $this->getInvitationStatusName($row->invite_status),
            data_get($user,'formatted_login_status') ? Carbon::parse(data_get($user,'user_rank'))->diffForHumans() : '-',
            data_get($user,'user_points',0),
            data_get($user,'user_rank'),
            data_get($user,'achievement_count',0),
            data_get($user,'challenges_progress_count',0),
            data_get($user,'challenge_paths_progress_count',0),
            data_get($user,'labs_progress_count',0),
            data_get($user,'lab_programs_progress_count',0),
            data_get($user,'resources_modules_progresses_count',0),
            data_get($user,'resources_groups_progresses_count',0),
            data_get($user,'resources_collections_progresses_count',0),
        ];
    }

    private function getInvitationStatusName($status): string
    {
        $statusMap = [
            '0' => 'invited',
            '1' => 'accepted',
            '2' => 'pending',
            '3' => 'declined',
            '4' => 'auto_created',
        ];

        return data_get($statusMap, $status, null);
    }

    /**
     * @return int[]
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 20,
            'D' => 15,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
            'M' => 30,
            'N' => 30,
            'O' => 30,
            'P' => 30,
        ];
    }
}
