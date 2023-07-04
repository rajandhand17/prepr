<?php

namespace App\Services;

use App\Models\SocialLink;
use App\Models\LabExternalLinks;
use DB;
class LabExternalLinksService
{
    public function createLabExternalLinks($request,$lab){
        try {

            if($request->has('external_links') && $request->get('external_link_ids')){
                if(count($request->external_link_ids) > 0 ){
                    foreach ($request->external_link_ids as $key => $value){
                        if (!empty($request->external_links[$key]) && !empty($request->external_link_ids[$key])) {
                            $labExternalLink=new LabExternalLinks;
                            $labExternalLink->lab_id =  $lab->id;
                            $labExternalLink->social_media_link= $request->external_links[$key];
                            $labExternalLink->social_link_id= $value;
                            $labExternalLink->save();
                        }
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
}
}
