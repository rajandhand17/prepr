<?php

namespace App\Http\Resources\Manage\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $inviteStatus = [
            '0' => 'invited',
            '1' => 'accepted',
            '2' => 'pending',
            '3' => 'declined',
            '4' => 'auto_created',
        ];

        return [
            'id'                         => data_get($this->organizationUser, 'id', '-'),
            'name'                       => data_get($this->organizationUser, 'full_name', $this->invitee_name),
            'username'                   => data_get($this->organizationUser, 'username'),
            'role'                       => $this->role,
            'email'                      => $this->email,
            'invitation_status'          => $inviteStatus[$this->invite_status],
            'account_activity'           =>  data_get($this->organizationUser, 'formatted_login_status', __('In Active')),
            'learning_points'            => data_get($this->organizationUser, 'user_points'),
            'learning_rank'              => data_get($this->organizationUser, 'user_rank'),
            'achievement_count'          => data_get($this->organizationUser, 'achievement_count'),
            'completion_count_by_module' => data_get($this->organizationUser, 'completion_count_by_module'),
        ];
    }
}
