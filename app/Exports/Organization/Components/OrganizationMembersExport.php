<?php

namespace App\Exports\Organization\Components;

use App\Models\Organization;
use App\Services\Manage\Report\OrganizationReportService;
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
            $user->full_name,
            $user->username,
            $row->role,
            $user->email,
            $this->getInvitationStatusName($row->invite_status),
            $user->login_status,
            $user->user_points,
            $user->user_rank,
            $user->achievement_count,
            $user->challenges_progress_count,
            $user->challenge_paths_progress_count,
            $user->labs_progress_count,
            $user->lab_programs_progress_count,
            $user->resources_modules_progresses_count,
            $user->resources_groups_progresses_count,
            $user->resources_collections_progresses_count,
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
