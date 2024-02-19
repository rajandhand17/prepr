<?php

namespace App\Http\Resources\Manage\Project;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessProjectListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request): array
    {
        $view_enabled = null;
        $challengeData = null;
        $status = 'yes';
        $liked = 'no';

        switch ($this->view_enabled) {
            case 'yes':
                $view_enabled = 'yes';
                break;
            case 'no':
                $view_enabled = 'no';
                break;
            default:
                $view_enabled = 'yes';
                break;
        }

        switch ($this->media_type) {
            case 'image':
                $media = $this->media;
                break;
            case 'embedded':
                $media = $this->getRawOriginal('media');
                break;
            default:
                $media = $this->media;
                break;
        }

        if ($this->challenge_id) {
            $fetchChallenge = ChallengeService::getChallengeBasedOnId($this->challenge_id);
            if ($fetchChallenge) {
                $projectDate = UtilityHelper::formatDateTime($this->created_at);
                $fetchChallengeDueDate = ChallengeService::fetchChallengeDueDate($fetchChallenge, $projectDate);
                $challengeData = [
                    'id'                => $fetchChallenge->id,
                    'uuid'              => $fetchChallenge->uuid,
                    'title'             => $fetchChallenge->title,
                    'slug'              => $fetchChallenge->slug,
                    'challenge_type'    => $fetchChallengeDueDate['timeline_type'],
                    'due_date'          => $fetchChallengeDueDate['submission_deadline_date'],
                ];
            }
        }

        switch ($this->status) {
            case '0':
                $status = 'no';
                break;
            case '1':
                $status = 'yes';
                break;
            default:
                $status = 'yes';
                break;
        }

        if ($this->likes()) {
            $liked = $this->likes() > 0 ? 'yes' : 'no';
        }

        return [
            'id'                    => $this->uuid,
            'language'              => $this->language,
            'user_id'               => $this->user_id,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'view_enabled'          => $view_enabled,
            'media_type'            => $this->media_type,
            'media'                 => $media,
            'status'                => $status,
            'liked'                 => $liked,
            'likes'                 => $this->likes(),
            'shares'                => $this->shares(),
            'favourite'             => $this->favourite(),
            'member_count'          => $this->getMembersCount(),
            'challenge_data'        => $challengeData,
            'updated_at'            => UtilityHelper::formatDateTime($this->updated_at),
        ];
    }
}
