<?php

namespace App\Exports\Lab\Components;

use App\Models\Lab;
use App\Services\Manage\Report\LabReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class LabMembersExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle, WithHeadings, WithMapping
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
        return 'Lab Members';
    }

    public function headings(): array
    {
        return [
            'Name', 'User Name', 'Email', 'Invitation Status', 'Account Activity', 'Lab Progress', 'Lab Achievement', 'Completed Challenges', 'Completed Challenge Paths', 'Completed Resource Modules', 'Completed Resource Groups', 'Completed Resource Collections',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return collect(data_get($this->labReportService->getPaginatedMembers($this->lab, false), 'list'));
    }

    public function map($row): array
    {
        $user = $row->user;

        return [
            $user->full_name,
            $user->username,
            $user->email,
            $this->getInvitationStatusName($row->invite_status),
            $user->login_status,
            $user->labProgressName,
            $user->userAchievements->first() ? $user->userAchievements->first()->title : '-',
            $user->challenges_progress_count,
            $user->challenge_paths_progress_count,
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
        ];
    }
}
