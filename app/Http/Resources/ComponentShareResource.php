<?php

namespace App\Http\Resources;

use App\Helpers\UtilityHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComponentShareResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($request->component, $request->slug);

        switch ($request->component) {
            case 'lab':
                $titleText = __('responses.share_url_lab').$checkComponentBasedOnSlug->title;
                break;

            case 'challenge':
                $titleText = __('responses.share_url_challenge').$checkComponentBasedOnSlug->title;
                break;

            case 'project':
                $titleText = __('responses.share_url_project').$checkComponentBasedOnSlug->title;
                break;
        }

        $encodedUrl = urlencode($this->resource);
        $encodedText = urlencode($titleText);
        $subjectText = __('responses.check_this_preprlabs');

        $shareLinks = [
            'facebook'  => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
            'twitter'   => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedText}",
            'whatsapp'  => "https://api.whatsapp.com/send?text={$encodedText}%20{$encodedUrl}",
            'email'     => 'mailto:?subject='.$subjectText.'&body='.rawurlencode($titleText."\n\nLink: ".$this->resource),
            'copyurl'   => $this->resource,
        ];

        $data = [
            [
                'type' => 'facebook',
                'url'  => $shareLinks['facebook'],
            ],
            [
                'type' => 'twitter',
                'url'  => $shareLinks['twitter'],
            ],
            [
                'type' => 'email',
                'url'  => $shareLinks['email'],
            ],
            [
                'type' => 'whatsapp',
                'url'  => $shareLinks['whatsapp'],
            ],
            [
                'type' => 'copyurl',
                'url'  => $shareLinks['copyurl'],
            ],
        ];

        return $data;
    }
}
