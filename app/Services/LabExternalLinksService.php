<?php

namespace App\Services;

use App\Models\SocialLink;
use App\Models\LabExternalLinks;
use DB;
class LabExternalLinksService
{
    public function store($request,$lab){
        try {
            DB::beginTransaction();
            foreach ($request->link_url as $key => $value) {
                if (!empty($request->link_url[$key]) && !empty($request->social_name[$key])) {
                    $social_links=SocialLink::select('id')->where("name",$request->social_name[$key])->first();
                    $social_link_id=$social_links->id;
                    $ExternalLinkUrl=LabExternalLinks::create([
                        'user_id' => auth()->user()->id,
                        'lab_id' => $lab->id,
                        'social_media_link' =>$value,
                        'social_link_id' =>  $social_link_id,
                    ]);
                }
            }
            DB::rollback();
            return false;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
}
}