<?php

namespace App\Console\Commands\Challenge;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAnnouncement;
use App\Models\ChallengeAnnouncementRecipient;
use App\Models\ChallengeSocialActivity;
use App\Services\AchievementService;
use App\Services\Manage\MemberManagementService;
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
            $currentDate = date('Y-m-d H:i:s'); // Getting current date and time
            $fetchPendingChallengeAnnouncementLists = ChallengeAnnouncement::select('challenge_announcements.challenge_id', 'challenge_announcements.subject', 'challenge_announcements.to_recipient_ids', 'challenge_announcements.sent_by', 'challenge_announcements.description', 'challenge_announcements.schedule_at', 'challenge_announcements.status','challenge_announcements.sent_status', 'challenges.title', 'challenges.slug', 'challenges.organization_id')
            ->where('sent_status', '0')
            ->join('challenges', 'challenge_announcements.challenge_id', '=', 'challenges.id')
            ->get();

            if ($fetchPendingChallengeAnnouncementLists->isNotEmpty()) {
                foreach ($fetchPendingChallengeAnnouncementLists as $pendingChallengeAnnouncement) {
                    $fetchInvitedChallengeUserIds = $fetchChallengeAchievementWinnerIds = $challengeFollowersIds = $fetchAutoAcceptedEmailsBasedData = collect();
                    $getRecipientList = ChallengeAnnouncementRecipient::whereIn('id', $pendingChallengeAnnouncement->to_recipient_ids)->pluck('title')->all();
                    // for Invited Challenge Participants
                    if (in_array("Invited Challenge Participants", $getRecipientList)) {
                        $fetchEmailArray = MemberManagementService::getMembersBasedOnComponentId('challenge', $pendingChallengeAnnouncement->challenge_id);
                        if ($fetchEmailArray->isNotEmpty()) {
                            $fetchInvitedChallengeUserIds = UserService::getUserIdsByEmail($fetchEmailArray);
                        }
                    }

                    // for Challenge Savers
                    if (in_array("Challenge Savers", $getRecipientList)) {
                        $challengeFollowersIds = ChallengeSocialActivity::where(['challenge_id' => $pendingChallengeAnnouncement->challenge_id, 'favourite' => '1'])->pluck('user_id');
                    }

                    // for Challenge Achievement Winners
                    if (in_array("Challenge Achievement Winners", $getRecipientList)) {
                        $fetchChallengeAchievementWinnerIds = AchievementService::fetchChallengeAchievementWinnerIds($pendingChallengeAnnouncement->challenge_id);
                    }

                    // for Auto-invite Accept
                    if (in_array("Auto-invite Accept", $getRecipientList)) {
                        $componentType = config('constants.module_component_type.challenge');
                        $fetchEmailArray = MemberManagementService::getAutoAcceptedComponentAndEmailsBasedData($pendingChallengeAnnouncement->challenge_id, $componentType);
                        if ($fetchEmailArray->isNotEmpty()) {
                            $fetchAutoAcceptedEmailsBasedData = UserService::getUserIdsByEmail($fetchEmailArray);
                        }
                    }
                    dd("in", $fetchInvitedChallengeUserIds->merge($fetchChallengeAchievementWinnerIds)->merge($challengeFollowersIds)->merge($fetchAutoAcceptedEmailsBasedData)->unique());
                }
            }
            dd($currentDate, $fetchPendingChallengeAnnouncementLists);

            $this->error('Challenge announcement command initiated');
        } catch (Exception $e) {
            dd($e);
            UtilityHelper::logError($e);
            $this->error('Challenge announcement not sent');

            return false;
        }
    }
}
