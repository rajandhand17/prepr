<?php

namespace App\Http\Resources\Project;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavouriteProjectListingResource extends JsonResource
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
        $$view_enabled = null;
        $download_enabled = null;
        $challengeData = null;
        $privacy = 'public';
        $liked = 'no';

        switch ($this->is_view_enabled) {
            case '0':
                $view_enabled = 'no';
                break;
            case '1':
                $view_enabled = 'yes';
                break;
            default:
                $view_enabled = 'no';
                break;
        }

        switch ($this->is_download_enabled) {
            case '0':
                $download_enabled = 'no';
                break;
            case '1':
                $download_enabled = 'yes';
                break;
            default:
                $download_enabled = 'no';
                break;
        }

        switch ($this->media_type) {
            case 'image':
                $media = $this->media;
                break;
            case 'embedded':
                $media = $this->getRawOriginal('media');
                break;
            case '2':
                $media = $this->media;
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

        $joinedStatus = 'no';
        if ($this->getJoinedStatus()) {
            switch ($this->getJoinedStatus() !== null) {
                case '0':
                    $joinedStatus = 'invited';
                    break;
                case '1':
                    $joinedStatus = 'yes';
                    break;
                case '2':
                    $joinedStatus = 'pending';
                    break;
                case '3':
                    $joinedStatus = 'no';
                    break;
                default:
                    $joinedStatus = 'no';
                    break;
            }
        }

        switch ($this->privacy) {
            case '0':
                $privacy = 'public';
                break;
            case '1':
                $privacy = 'private';
                break;
            default:
                $privacy = 'public';
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
            'is_view_enabled'       => $view_enabled,
            'is_download_enabled'   => $download_enabled,
            'media_type'            => $this->media_type,
            'media'                 => $media,
            'privacy'               => $privacy,
            'liked'                 => $liked,
            'likes'                 => $this->likes(),
            'shares'                => $this->shares(),
            'favourite'             => $this->favourite(),
            'member_count'          => $this->getMembersCount(),
            'joinedStatus'          => $joinedStatus,
            'challenge_id'          => $challengeData,
            'updated_at'            => UtilityHelper::formatDateTime($this->updated_at),
        ];
    }
}
