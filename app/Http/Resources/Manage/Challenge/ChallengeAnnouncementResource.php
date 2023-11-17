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
        $schedule_at = [];
        $to_recipient_ids = [];
        foreach ($this->to_recipient_ids as $to_recipient_id) {
            $challenge_announcement_recipient = ChallengeAnnouncementService::getChallengeAnnouncementByID($request->language, $to_recipient_id);
            $to_recipient_ids[$challenge_announcement_recipient->id] = $challenge_announcement_recipient->title;
        }

        if ($this->schedule_at) {
            $schedule_at = UtilityHelper::formatDateTime($this->schedule_at);
        }

        switch ($this->sent_by) {
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

        switch ($this->sent_status) {
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

        switch ($this->status) {
            case '0':
                $status = 'send';
                break;
            case '1':
                $status = 'draft';
                break;
            case '2':
                $status = 'scheduled';
                break;
            default:
                $status = 'send';
                break;
        }

        return [
            'id'                    => $this->id,
            'subject'               => $this->subject,
            'sent_by'               => $sent_by,
            'description'           => $this->description,
            'schedule_at'           => $schedule_at,
            'status'                => $status,
            'sent_status'           => $sent_status,
            'to_recipient_ids'      => json_decode(json_encode($to_recipient_ids)),
        ];
    }
}
