<?php

namespace App\Services;

use App\Models\SocialLink;

class SocialLinkService
{
    public function getSocialLinks($language = 'en', $search = null)
    {
        try {
            $social_link_list = SocialLink::select('id', 'title', 'icon');
            //Search categories based on user input
            if ($search != null) {
                $social_link_list = $social_link_list->where('title', 'like', '%' . $search . '%');
            }

            //take 20 results based from the table
            $social_link_list = $social_link_list->take(20)->get();

            //check if there are any results
            if (!$social_link_list->isEmpty()) {
                return $social_link_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
