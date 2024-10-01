<?php

namespace App\Services\Manage;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengeAnnouncement;
use App\Models\ChallengeAnnouncementRecipient;
use App\Models\ChallengeSocialActivity;
use App\Notifications\SendChallengeAnnouncementNotification;
use App\Services\AchievementService;
use App\Services\Chat\ConversationService;
use App\Services\Chat\MessageService;
use App\Services\ProjectService;
use App\Services\Public\ProjectMemberManagementService;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Schema;

class ChallengeAnnouncementService
{
    public function createChallengeAnnouncement($challengeId, $request)
    {
        try {
            $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.both');
            switch ($request->sent_by) {
                case 'email':
                    $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.email');
                    break;
                case 'inbox':
                    $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.inbox');
                    break;
                case 'both':
                    $sendAnnouncementChannelMedium = config('constants.challenge_announcement_by.both');
                    break;
            }

            $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.send');
            switch ($request->status) {
                case 'send':
                    $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.send');
                    break;
                case 'draft':
                    $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.draft');
                    break;
                case 'scheduled':
                    $sendAnnouncementSendStatus = config('constants.challenge_announcement_send_status.scheduled');
                    break;
            }

            $schedule_date = $request->schedule_at !== null ? date('Y-m-d H:i:s', strtotime($request->schedule_at)) : null;

            $challengeAnnouncement = new ChallengeAnnouncement();
            $challengeAnnouncement->challenge_id = $challengeId;
            $challengeAnnouncement->subject = $request->subject;
            $challengeAnnouncement->to_recipient_ids = $request->to_recipient_ids;
            $challengeAnnouncement->sent_by = $sendAnnouncementChannelMedium;
            $challengeAnnouncement->description = $request->description;
            $challengeAnnouncement->schedule_at = $schedule_date;
            $challengeAnnouncement->status = $sendAnnouncementSendStatus;
            $challengeAnnouncement->save();

            return $challengeAnnouncement;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeAnnouncementByID($language = 'en', $announcement_recipient_id)
    {
        try {
            if ($language == 'en') {
                $announcement_recipient = ChallengeAnnouncementRecipient::select('id', 'title');
            } else {
                //get column title based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('to_recipient_ids', $column_name)) {
                    return false;
                }
                $announcement_recipient = ChallengeAnnouncementRecipient::select('id', $column_name.' as title');
            }
            $challenge_announcement = $announcement_recipient->find($announcement_recipient_id);

            return $challenge_announcement;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteChallengeAnnouncement($challengeAnnouncementId)
    {
        try {
            ChallengeAnnouncement::find($challengeAnnouncementId)->delete();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchChallengeAnnouncement($challengeAnnouncementId)
    {
        try {
            $fetchChallengeAnnouncement = ChallengeAnnouncement::find($challengeAnnouncementId);

            return $fetchChallengeAnnouncement;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function sendChallengeAnnouncement($pendingChallengeAnnouncementId)
    {
        try {
            $pendingChallengeAnnouncement = ChallengeAnnouncement::select('challenge_announcements.id', 'challenge_announcements.challenge_id', 'challenge_announcements.subject', 'challenge_announcements.to_recipient_ids', 'challenge_announcements.sent_by', 'challenge_announcements.description', 'challenge_announcements.schedule_at', 'challenge_announcements.status', 'challenge_announcements.sent_status', 'challenges.title', 'challenges.slug', 'challenges.organization_id')
            ->join('challenges', 'challenge_announcements.challenge_id', '=', 'challenges.id')
            ->where('challenge_announcements.id', $pendingChallengeAnnouncementId)
            ->first();

            $currentLocationBasedTime = now(); // Sending using UTC format
            if ($pendingChallengeAnnouncement->schedule_at < $currentLocationBasedTime) {
                $fetchChallengeDetail = ChallengeService::getChallengeBasedOnSlug($pendingChallengeAnnouncement->slug);
                $fetchInvitedChallengeUserIds = $fetchChallengeWinnerAchievementUserIds = $challengeFollowersIds = $fetchAutoAcceptedEmailsBasedData = $fetchInvitedLabUserIds = $fetchAssociatedProjectUserIds = $fetchSubmittedProjectUserIds = $fetchChallengeParticipationAchievementUserIds = collect();
                $getRecipientList = ChallengeAnnouncementRecipient::whereIn('id', $pendingChallengeAnnouncement->to_recipient_ids)->pluck('title')->all();
                // for Invited Challenge Participants
                if (in_array('Invited Challenge Participants', $getRecipientList)) {
                    $fetchEmailArray = MemberManagementService::getMembersBasedOnComponentId('challenge', $pendingChallengeAnnouncement->challenge_id);
                    if ($fetchEmailArray->isNotEmpty()) {
                        $fetchInvitedChallengeUserIds = UserService::getUserIdsByEmail($fetchEmailArray);
                    }
                }

                // for Challenge Savers
                if (in_array('Challenge Savers', $getRecipientList)) {
                    $challengeFollowersIds = ChallengeSocialActivity::where(['challenge_id' => $pendingChallengeAnnouncement->challenge_id, 'favourite' => '1'])->pluck('user_id');
                }

                // for Challenge Achievement Winners
                if (in_array('Challenge Achievement Winners', $getRecipientList)) {
                    $achievementType = config('constants.user_achievement_type.winner_award');
                    $fetchChallengeWinnerAchievementUserIds = AchievementService::fetchChallengeAchievementUserIds($pendingChallengeAnnouncement->challenge_id, $achievementType);
                }

                // for Auto-invite Accept
                if (in_array('Auto-invite Accept', $getRecipientList)) {
                    $componentType = config('constants.module_component_type.challenge');
                    $fetchEmailArray = MemberManagementService::getAutoAcceptedComponentAndEmailsBasedData($pendingChallengeAnnouncement->challenge_id, $componentType);
                    if ($fetchEmailArray->isNotEmpty()) {
                        $fetchAutoAcceptedEmailsBasedData = UserService::getUserIdsByEmail($fetchEmailArray);
                    }
                }

                // for Associated Lab Users
                if (in_array('Associated Lab Users', $getRecipientList)) {
                    $fetchLabAssociatedToChallenge = ComponentAssociationService::fetchLabIdsAssociatedChallengeId($fetchChallengeDetail->id);
                    if ($fetchLabAssociatedToChallenge->isNotEmpty()) {
                        $moduleType = config('constants.module_component_type.lab');
                        $fetchAcceptedLabMembers = MemberManagementService::totalActiveMembersCountBasedOnModuleIds($fetchLabAssociatedToChallenge, $moduleType);
                        if ($fetchAcceptedLabMembers->isNotEmpty()) {
                            $fetchInvitedLabUserIds = UserService::getUserIdsByEmail($fetchAcceptedLabMembers->pluck('email'));
                        }
                    }
                }

                // for Associated Project Users
                if (in_array('Associated Project Users', $getRecipientList)) {
                    $fetchChallengeProjectIds = ProjectService::fetchProjectIdsBasedOnChallenge($pendingChallengeAnnouncement->challenge_id);
                    if ($fetchChallengeProjectIds->isNotEmpty()) {
                        $fetchAcceptedProjectMemberEmails = ProjectMemberManagementService::fetchAcceptedProjectMemberEmails($fetchChallengeProjectIds);
                        if ($fetchAcceptedProjectMemberEmails->isNotEmpty()) {
                            $fetchAssociatedProjectUserIds = UserService::getUserIdsByEmail($fetchAcceptedProjectMemberEmails);
                        }
                    }
                }

                // for Submitted Project Users
                if (in_array('Submitted Project Users', $getRecipientList)) {
                    $fetchSubmittedProjectUserIds = ProjectService::fetchCompletedChallengeUserIds($pendingChallengeAnnouncement->challenge_id);
                }

                // for Participant Achievement Winners
                if (in_array('Participant Achievement Winners', $getRecipientList)) {
                    $achievementType = config('constants.user_achievement_type.participation_award');
                    $fetchChallengeParticipationAchievementUserIds = AchievementService::fetchChallengeAchievementUserIds($pendingChallengeAnnouncement->challenge_id, $achievementType);
                }

                $recipientList = $fetchInvitedChallengeUserIds->merge($fetchChallengeWinnerAchievementUserIds)->merge($challengeFollowersIds)->merge($fetchAutoAcceptedEmailsBasedData)->merge($fetchInvitedLabUserIds)->merge($fetchAssociatedProjectUserIds)->merge($fetchSubmittedProjectUserIds)->merge($fetchChallengeParticipationAchievementUserIds)->unique();
                if ($recipientList->isEmpty()) {
                    $markAnnouncementCompleted = $this->markAnnouncementCompleted($pendingChallengeAnnouncement->id);
                }

                // for email medium
                if ($pendingChallengeAnnouncement->sent_by == '0') {
                    $sendChallengeAnnouncement = $this->sendChallengeAnnouncementViaEmail($pendingChallengeAnnouncement, $fetchChallengeDetail, $recipientList);
                }

                // for inbox medium
                if ($pendingChallengeAnnouncement->sent_by == '1') {
                    $sendChallengeAnnouncement = $this->sendChallengeAnnouncementViaInbox($pendingChallengeAnnouncement, $fetchChallengeDetail, $recipientList);
                }

                // for both mediums
                if ($pendingChallengeAnnouncement->sent_by == '2') {
                    $sendAnnouncementViaEmail = $this->sendChallengeAnnouncementViaEmail($pendingChallengeAnnouncement, $fetchChallengeDetail, $recipientList);
                    $sendAnnouncementViaInbox = $this->sendChallengeAnnouncementViaInbox($pendingChallengeAnnouncement, $fetchChallengeDetail, $recipientList);
                    if ($sendAnnouncementViaEmail && $sendAnnouncementViaInbox) {
                        $sendChallengeAnnouncement = true;
                    }
                }

                if ($sendChallengeAnnouncement) {
                    $markAnnouncementCompleted = $this->markAnnouncementCompleted($pendingChallengeAnnouncement->id);
                    if (!$markAnnouncementCompleted) {
                        throw new Exception('Failed to announcement'.$pendingChallengeAnnouncement->id);
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function markAnnouncementCompleted($announcementId)
    {
        try {
            $fetchPendingChallengeAnnouncement = ChallengeAnnouncementService::fetchChallengeAnnouncement($announcementId);
            $fetchPendingChallengeAnnouncement->status = '0';
            $fetchPendingChallengeAnnouncement->sent_status = '1';
            $fetchPendingChallengeAnnouncement->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function sendChallengeAnnouncementViaEmail($announcementData, $challengeDetail, $recipientList)
    {
        try {
            if ($recipientList->isNotEmpty()) {
                foreach ($recipientList as $userId) {
                    $fetchUserData = UserService::getUserById($userId);
                    $fetchOrganizationData = OrganizationService::getOrganizationExistBasedOnId($challengeDetail->organization_id);
                    if ($fetchUserData && $fetchOrganizationData) {
                        $email_detail = [
                            'subject'           => $announcementData->subject,
                            'email'             => $fetchUserData->email,
                            'name'              => $fetchUserData->full_name,
                            'challenge'         => $challengeDetail,
                            'organization'      => $fetchOrganizationData,
                            'announcementData'  => $announcementData,
                        ];
                        $fetchUserData->notify(new SendChallengeAnnouncementNotification($email_detail));
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function sendChallengeAnnouncementViaInbox($announcementData, $challengeDetail, $recipientList)
    {
        try {
            if ($recipientList->isNotEmpty()) {
                $fetchUserNames = UserService::getUserNamesByIds($recipientList)->all();
                $type = 'announcement';
                // Prepare the response
                $conversationData = [
                    'usernames'     => $fetchUserNames,
                    'type'          => $type,
                    'created_by'    => $challengeDetail->user_id,
                ];
                if (!empty($fetchUserNames)) {
                    $conversations = new ConversationService();
                    $createConversation = $conversations->create($conversationData);
                    if ($createConversation) {
                        $messageData = [
                            'conversation_id' => $createConversation->id,
                            'message'         => data_get($announcementData, 'description'),
                            'sent_by'         => $challengeDetail->user_id,
                        ];
                        $createMessage = MessageService::sendAnnouncement($messageData);
                        if (!$createMessage) {
                            return false;
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
