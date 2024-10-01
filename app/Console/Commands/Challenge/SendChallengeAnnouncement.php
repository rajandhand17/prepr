<?php

namespace App\Console\Commands\Challenge;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAnnouncement;
use App\Models\ChallengeAnnouncementRecipient;
use App\Models\ChallengeSocialActivity;
use App\Notifications\SendChallengeAnnouncementNotification;
use App\Services\AchievementService;
use App\Services\Chat\ConversationService;
use App\Services\Chat\MessageService;
use App\Services\Maestro\ComponentAssociationService;
use App\Services\Manage\ChallengeAnnouncementService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\OrganizationService;
use App\Services\ProjectService;
use App\Services\Public\ProjectMemberManagementService;
use App\Services\UserService;
use Exception;
use Illuminate\Console\Command;

class SendChallengeAnnouncement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenge:send-challenge-announcement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to send challenge announcement';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->error('Challenge announcement command initiated');
            $fetchPendingChallengeAnnouncementLists = ChallengeAnnouncement::select('challenge_announcements.id', 'challenge_announcements.challenge_id', 'challenge_announcements.subject', 'challenge_announcements.to_recipient_ids', 'challenge_announcements.sent_by', 'challenge_announcements.description', 'challenge_announcements.schedule_at', 'challenge_announcements.status', 'challenge_announcements.sent_status', 'challenges.title', 'challenges.slug', 'challenges.organization_id')
            ->where('sent_status', '0')
            ->join('challenges', 'challenge_announcements.challenge_id', '=', 'challenges.id')
            ->get();

            if ($fetchPendingChallengeAnnouncementLists->isNotEmpty()) {
                foreach ($fetchPendingChallengeAnnouncementLists as $pendingChallengeAnnouncement) {
                    $userLocationBasedTime = now(); // Sending using UTC format
                    if ($pendingChallengeAnnouncement->schedule_at < $userLocationBasedTime) {
                        $fetchChallengeDetail = ChallengeService::getChallengeBasedOnSlug($pendingChallengeAnnouncement->slug);
                        $fetchInvitedChallengeUserIds = $fetchChallengeWinnerAchievementUserIds = $challengeFollowersIds = $fetchAutoAcceptedEmailsBasedData = $fetchInvitedLabUserIds = $fetchAssociatedProjectUserIds = $fetchChallengeParticipationAchievementUserIds = collect();
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
                            $fetchLabAssociatedToChallenge = ComponentAssociationService::getChallengeAssociatedLab($fetchChallengeDetail);
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
                }
            }

            $this->error('Challenge announcement command completed');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error('Challenge announcement sending failure');

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
            $this->error('Challenge announcement not marked completed');

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
            $this->error('Challenge announcement not sent via email');

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
            $this->error('Challenge announcement not sent via inbox');

            return false;
        }
    }
}
