<?php

namespace App\Repositories\Api\Manage\ChannelApi;

use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\UserService;

class ChannelApiRepository implements ChannelApiInterface
{
    public function getLabs($type, $organization, $user)
    {
        try {
            if ($type === 'community') {
                return LabService::getLabsBasedOnOrganizationId($organization->id);
            }

            if ($type === 'user') {
                $labIdsFromMemberManagement = MemberManagementService::getMemberManagementByModuleAndEmail(config('constants.member_management_component_type.lab'), $user->email)->pluck('module_id');
                $labsIds = LabService::getLabBasedOnUserId($user->id)->pluck('id');

                return LabService::getLabsBasedOnIds($labsIds->merge($labIdsFromMemberManagement)->unique());
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getChallenges($type, $organization, $user)
    {
        try {
            if ($type === 'community') {
                return ChallengeService::getChallengeBasedOnOrganizationId($organization->id);
            }

            if ($type === 'user') {
                $challengeIdsFromMemberManagement = MemberManagementService::getMemberManagementByModuleAndEmail(config('constants.member_management_component_type.challenge'), $user->email)->pluck('module_id');
                $challengeIds = ChallengeService::getChallengeBasedOnUserId($user->id)->pluck('id');

                return ChallengeService::getPaginatedChallengeBasedOnIds($challengeIds->merge($challengeIdsFromMemberManagement)->unique());
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function assignUserToLab($users, $lab)
    {
        try {
            $magnetUsers = UserService::registerOrUpdateMagnetUsers($users);

            if (!$magnetUsers) {
                return false;
            }

            $formattedMembersList = collect($magnetUsers)->map(function ($user) {
                return [
                    'type'          => config('constants.member_management_type.invite'),
                    'invite_status' => '1',
                    'invite_type'   => config('constants.member_management_invite_type.email'),
                    'invitee_name'  => data_get($user, 'name'),
                    'invitee_email' => data_get($user, 'email'),
                    'role'          => 'User',
                ];
            });

            $inviteMember = MemberManagementService::addMembers(
                $lab,
                'lab',
                (object) [
                    'auto_invite'  => 'Yes',
                    'email_status' => 'sent',
                    'subject_line' => 'Invitation to Learn Lab '.$lab->title,
                    'email_body'   => 'Welcome to the '.$lab->title.'! You will find a lot of the key information here, including the relevant challenges, resources, and discussion. Check back regularly for updates.',
                ],
                $formattedMembersList
            );

            if (!$inviteMember) {
                return false;
            }

            return $inviteMember;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
