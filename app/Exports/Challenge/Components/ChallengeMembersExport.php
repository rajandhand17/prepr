<?php

namespace App\Exports\Challenge\Components;

use App\Models\Challenge;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\Report\ChallengeReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Tightenco\Collect\Support\Collection;

class ChallengeMembersExport implements FromCollection, withColumnWidths, WithStrictNullComparison, WithTitle, WithHeadings, WithMapping
{
    private $challenge;
    protected $challengeReportService;

    public function __construct(Challenge $challenge)
    {
        $this->challenge = $challenge;
        $this->challengeReportService = new ChallengeReportService();
    }

    public function title(): string
    {
        return 'Challenge Members';
    }

    public function headings(): array
    {
        return [
            'Name', 'User Name', 'Email', 'Invitation Status', 'Account Activity', 'Project Title', 'Project Start Date', 'Project Progress', 'Achievement', 'Images/Videos', 'Files', 'Discussions',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection|Collection
     */
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return collect(data_get($this->challengeReportService->getPaginatedMembers($this->challenge, false), 'list'));
    }

    public function map($row): array
    {
        $user = $row->user;
        $project = $user->userProjects->first();
        $challenge = ChallengeService::getChallengeBasedOnId($user->challengeDiscussions->first()->module_id);

        return [
            $user ? $user->full_name : '-',
            $user ? $user->username : '-',
            $user ? $user->email : '-',
            $this->getInvitationStatusName($row->invite_status),
            $user ? $user->formatted_login_status: null,
            $project ? $project->title : '-',
            $project ? $project->created_at : '-',
            $project ? $this->getProjectSubmitStatus($challenge, $project) : '-',
            $user ? $user->userAchievements->first() ? $user->userAchievements->first()->title : 0 : 0,
            $project ? $project->get_project_images_count + $project->get_project_videos_count : 0,
            $project ? $project->get_project_docs_count : 0,
            $user ? $user->challenge_discussions_count : 0,
        ];
    }

    private function getProjectSubmitStatus($challenge, $userProject): string
    {
        $due_dates = ChallengeService::fetchChallengeDueDate($challenge, $userProject->created_at);

        return match (data_get($due_dates, 'submission_status')) {
            'late_submission' => 'Late Submission',
            'submission'      => 'Submitted',
            'not_allowed'     => 'Not Allowed',
            'deadline_missed' => 'Deadline Missed',
            default           => data_get($due_dates, 'submission_status'),
        };
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
