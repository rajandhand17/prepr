<?php

namespace App\Http\Resources\Manage\Challenge;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeAnnouncementService;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeAnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayal|\JsonSerializable
     */
    public function toArray($request)
    {
        $challenge_announcement = null;
        if ($this->challenge_announcement) {
            $challenge_announcement = $this->challenge_announcement->map(function ($item) {
                $to_recipient_ids = [];
                foreach ($item->to_recipient_ids as $to_recipient_id) {
                    $challenge_announcement_recipient = ChallengeAnnouncementService::getChallengeAnnouncementByID($this->language, $to_recipient_id);
                    $to_recipient_ids[$challenge_announcement_recipient->id] = $challenge_announcement_recipient->title;
                }

                switch ($item->sent_by) {
                    case '0':
                        $sent_by = 'email';
                        break;
                    case '1':
                        $sent_by = 'inbox';
                        break;
                    case '2':
                        $sent_by = 'both';
                        break;
                    default:
                        $sent_by = 'both';
                        break;
                }

                switch ($item->sent_status) {
                    case '0':
                        $sent_status = 'send';
                        break;
                    case '1':
                        $sent_status = 'pending';
                        break;
                    default:
                        $sent_status = 'pending';
                        break;
                }

                switch ($item->status) {
                    case '0':
                        $status = 'send';
                        break;
                    case '1':
                        $status = 'draft';
                        break;
                    default:
                        $status = 'send';
                        break;
                }

                return [
                    'subject'               => $item->subject,
                    'sent_by'               => $sent_by,
                    'description'           => $item->description,
                    'schedule_at'           => UtilityHelper::formatDateTime($item->schedule_at),
                    'status'                => $status,
                    'sent_status'           => $sent_status,
                    'to_recipient_ids'      => json_decode(json_encode($to_recipient_ids)),
                ];
            });
        }

        return [
            'slug'                          => $this->slug,
            'title'                         => $this->title,
            'challenge_announcement'        => $challenge_announcement,
        ];
    }
}
