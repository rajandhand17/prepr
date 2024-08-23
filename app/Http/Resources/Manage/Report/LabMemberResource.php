<?php

namespace App\Http\Resources\Manage\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user;

        return [
            'id'                         => data_get($user, 'id'),
            'profile_image'              => data_get($user, 'profile_image'),
            'full_name'                  => data_get($user, 'full_name', $this->invitee_name),
            'email'                      => data_get($user, 'email', $this->email),
            'username'                   => data_get($user, 'username', ''),
            'invitation_status'          => $this->formatInvitationStatus($this->invite_status) ?? '-',
            'activity'                   => data_get($user, 'last_login_date'),
            'lab_progress'               => $user ? $this->formatLabProgress($user->labProgresses?->first()->status) : '-',
            'achievement'                => $user ? $user->userAchievements->first()->title ?? '-' : '-',
            'completion_count_by_module' => [
                'total_challenges_completed'           => data_get($user, 'challenges_progress_count', 0),
                'total_challenge_paths_completed'      => data_get($user, 'challenge_paths_progress_count', 0),
                'total_resource_modules_completed'     => data_get($user, 'resources_modules_progresses_count', 0),
                'total_resource_groups_completed'      => data_get($user, 'resources_groups_progresses_count', 0),
                'total_resource_collections_completed' => data_get($user, 'resources_collections_progresses_count', 0),
            ],
        ];
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

    private function formatLabProgress(string|null $progress)
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
