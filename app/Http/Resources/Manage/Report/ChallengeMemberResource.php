<?php

namespace App\Http\Resources\Manage\Report;

use App\Services\Manage\ChallengeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $userProject = $this->user?->userProjects->first() ?? null;
        $challenge = $request->challenge;

        return [
            'id'                 => data_get($user, 'id'),
            'profile_image'      => data_get($user, 'profile_image'),
            'full_name'          => data_get($user, 'full_name', $this->invitee_name),
            'username'           => data_get($user, 'username'),
            'email'              => data_get($user, 'email', $this->email),
            'invitation_status'  => $this->formatInvitationStatus($this->invite_status) ?? null,
            'activity'           => data_get($user, 'formatted_login_status', __('In Active')),
            'project_title'      => $userProject ? data_get($userProject, 'title') : null,
            'project_start_date' => $userProject ? data_get($userProject, 'created_at') : null,
            'project_progress'   => $userProject ? $this->getProjectSubmitStatus($challenge, $userProject) : null,
            'achievement'        => $user ? $user->userAchievements->first()?->title ?? null : null,
            'project_item_count' => [
                'images_videos' => $userProject ? $userProject->get_project_images_count + $userProject->get_project_videos_count : 0,
                'files'         => $userProject ? $userProject->get_project_docs_count : 0,
                'discussions'   => data_get($user, 'challenge_discussions_count', 0),
            ],
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

    private function formatInvitationStatus(string $status): string
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

    private function formatProgress(string|null $progress)
    {
        $statusMap = [
            '0' => 'Not Started',
            '1' => 'In Progress',
            '2' => 'Completed',
        ];

        if (!$progress) {
            return $statusMap[0];
        }

        return data_get($statusMap, $progress);
    }
}
