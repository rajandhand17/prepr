<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Favorite;
use App\Models\Lab;
use HiFolks\RandoPhp\Randomize;

class LabService
{
    public function createLab($request, $upload_cover_image)
    {
        try {
            $status = config('constants.lab_status.draft');
            switch($request->status) {
                case 'draft':
                    $status = config('constants.lab_status.draft');
                    break;
                case 'publish':
                    $status = config('constants.lab_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.lab_status.archive');
                    break;
                default:
                    $status = config('constants.lab_status.draft');
                    break;
            }
            $privacy = config('constants.lab_privacy.no');
            switch($request->privacy) {
                case 'yes':
                    $privacy = config('constants.lab_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.lab_privacy.no');
                    break;
                default:
                    $privacy = config('constants.lab_privacy.yes');
                    break;
            }
            $model = new Lab();
            $slug = UtilityHelper::generateSlug($request->title, $model);

            $lab = new Lab();
            $lab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $lab->language = $request->language;
            $lab->user_id = auth()->user()->id;
            $lab->organization_id = $request->organization_id;
            $lab->category_id = $request->category_id;

            $lab->slug = $slug;
            $lab->title = $request->title;
            $lab->description = $request->description;
            $lab->privacy = $privacy;

            $lab->media_type = 'image';
            $lab->media = $upload_cover_image;

            $lab->status = ($request->request_type == 'draft') ? '0' : (($request->request_type == 'publish') ? '1' : '2');

            $lab->total_share = 0;

            $lab->is_auto_created = '0';

            $lab->is_resource_sequential = ($request->is_resource_sequential == 'yes') ? '1' : '0';
            $lab->is_sequential = ($request->is_sequential == 'yes') ? '1' : '0';
            $lab->is_achievement_enabled = ($request->is_achievement_enabled == 'yes') ? '1' : '0';
            $lab->is_notification_enabled = ($request->is_notification_enabled == 'yes') ? '1' : '0';
            $lab->is_verified = '0';

            $lab->save();

            return $lab;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadCoverImage($image)
    {
        try {
            $uploadLabCoverImage = FileUploadHelper::uploadImageToS3($image, 'lab');
            if ($uploadLabCoverImage == false) {
                return false;
            }

            return $uploadLabCoverImage;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabList($request)
    {
        try {
            $labList = Lab::select();
            $labList = $this->filterLabList($labList, $request);

            return $labList;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterLabList($labList, $request)
    {
        try {
            if (isset($request->privacy) && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'yes':
                        $privacy = config('constants.lab_privacy.yes');
                        break;
                    case 'no':
                        $privacy = config('constants.lab_privacy.no');
                        break;
                    default:
                        $privacy = config('constants.lab_privacy.no');
                }
                $labList = $labList->where('privacy', $privacy);
            }
            $labList = $labList->get();

            return $labList;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabDetails($slug)
    {
        try {
            $labDetails = Lab::where('slug', $slug)->first();
            if ($labDetails) {
                return $labDetails;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateLab($lab_id, $request, $upload_cover_image)
    {
        try {
            $lab = Lab::find($lab_id)->first();
            $privacy = $lab->privacy;
            if ($request->has('privacy')) {
                switch($request->privacy) {
                    case 'yes':
                        $privacy = config('constants.lab_privacy.yes');
                        break;
                    case 'no':
                        $privacy = config('constants.lab_privacy.no');
                        break;
                    default:
                        $privacy = config('constants.lab_privacy.yes');
                        break;
                }
            }
            $lab->language = ($request->has('language')) ? $request->language : $lab->language;
            $lab->organization_id = ($request->has('organization_id')) ? $request->organization_id : $lab->organization_id;
            $lab->category_id = ($request->has('category_id')) ? $request->category_id : $lab->category_id;
            $lab->title = ($request->has('title')) ? $request->title : $lab->title;
            $lab->description = ($request->has('description')) ? $request->description : $lab->description;
            $lab->privacy = $privacy;
            $lab->media_type = 'image';
            $lab->media = $upload_cover_image;
            $lab->status = ($request->request_type == 'draft') ? '0' : (($request->request_type == 'publish') ? '1' : '2');
            $lab->is_resource_sequential = ($request->has('is_resource_sequential')) ? (($request->is_resource_sequential == 'yes') ? '1' : '0') : $lab->is_resource_sequential;
            $lab->is_sequential = ($request->has('is_sequential')) ? (($request->is_sequential == 'yes') ? '1' : '0') : $lab->is_sequential;
            $lab->is_achievement_enabled = ($request->has('is_achievement_enabled')) ? (($request->is_achievement_enabled == 'yes') ? '1' : '0') : $lab->is_achievement_enabled;
            $lab->is_notification_enabled = ($request->has('is_achievement_enabled')) ? (($request->is_notification_enabled == 'yes') ? '1' : '0') : $lab->is_achievement_enabled;
            $lab->save();

            return $lab;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteLab($lab_id)
    {
        try {
            $lab = Lab::where('id', $lab_id)->delete();
            
            if (!$lab) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateLabCoverImage($request)
    {
        try {
            $profile_image_path = FileUploadHelper::uploadbase64ImageToS3($request->cover_image, 'organization');
            if ($profile_image_path == false) {
                return false;
            }

            return $profile_image_path;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checklabSlug = Lab::where('slug', $slug)->first();
            if ($checklabSlug) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function checkActivity($activity,$slug,$request)
    {
        try {
            switch ($activity){
                case 'like':
                $checklabLike = Favorite::where([
                    ['refence_id','=', $request['refence_id']],
                    ['refence_type','=', $request['refence_type']],
                    ['user_id','=', auth()->user()->id],
                    ['is_like','=', '1'],
                ])->first();
                if (!$checklabLike) {
                    return true;
                }
                return false;    
                break;
                case 'dislike':
                    $checklabUnlike = Favorite::where([
                        ['refence_id','=', $request['refence_id']],
                        ['refence_type','=', $request['refence_type']],
                        ['user_id','=', auth()->user()->id],
                        ['is_like','=', '0'],
                    ])->first();
                    if (!$checklabUnlike) {
                        return true;
                    }
                    return false;
                    break;
                case 'follow':
                    $checklabFollow = Favorite::where([
                        ['refence_id','=', $request['refence_id']],
                        ['refence_type','=', $request['refence_type']],
                        ['user_id','=', auth()->user()->id],
                        ['is_follow','=', '1'],
                    ])->first();
                    if (!$checklabFollow){
                        return true;
                    }
                    return false;
                    break;
                case 'un-follow':
                    
                    $checklabUnFollow = Favorite::where([
                        ['refence_id','=', $request['refence_id']],
                        ['refence_type','=', $request['refence_type']],
                        ['user_id','=', auth()->user()->id],
                        ['is_follow','=', '0'],
                    ])->first();
                    if (!$checklabUnFollow){
                        return true;
                    }
                    return false;
                    break;
                case 'favorite':
                    $checklabFavorite = Favorite::where([
                        ['refence_id','=', $request['refence_id']],
                        ['refence_type','=', $request['refence_type']],
                        ['user_id','=', auth()->user()->id],
                        ['is_favorite','=', '1'],
                    ])->first();
                    if (!$checklabFavorite){
                        return true;
                    }
                    return false;
                    break;
                case 'un-favorite':
                    $checklabUnFavorite = Favorite::where([
                        ['refence_id','=', $request['refence_id']],
                        ['refence_type','=', $request['refence_type']],
                        ['user_id','=', auth()->user()->id],
                        ['is_favorite','=', '0'],
                    ])->first();
                    if (!$checklabUnFavorite){
                        return true;
                    }
                    return false;
                    break;
                default:
                dd("default");
                    return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

public function checkNameExistsOrNot($title)
{
    try {
        $checklabName = Lab::where('title', $title)->first();
        if ($checklabName) {
            return true;
        }

        return false;
    } catch (\Exception $e) {
        return false;
    }
}

public static function getLabExistBasedOnSlug($slug)
{
    try {
        $lab = Lab::where('slug', $slug)->first();
        if ($lab != null) {
           
            return $lab;
        }

        return false;
    } catch (\Exception $e) {
        return false;
    }
}

public function checkExistsOrNot($activity,$request){
    try {
        $checkFavouriteExistsOrNot = Favorite::where([
            ['refence_id','=', $request['refence_id']],
            ['refence_type','=', $request['refence_type']],
            ['user_id','=', auth()->user()->id],
        ])->first();
        if($checkFavouriteExistsOrNot){
            return $checkFavouriteExistsOrNot;
        }
        return false;
    } catch (\Exception $e){
        return false;
    }
}

public function storeLabActivity($activity,$request)
{
    try {
        switch ($activity){
            case 'like':
                    $likeLab=new Favorite();
                    $likeLab->refence_id=$request['refence_id'];
                    $likeLab->refence_type=$request['refence_type'];
                    $likeLab->user_id=auth()->user()->id;
                    $likeLab->is_like='1';
                    if($likeLab->save()){
                        return true;
                    }
                        return false;
                break;
            case 'dislike':
                    $likeLab=new Favorite();
                    $likeLab->refence_id=$request['refence_id'];
                    $likeLab->refence_type=$request['refence_type'];
                    $likeLab->user_id=auth()->user()->id;
                    $likeLab->is_like='0';
                    if($likeLab->save()){
                        return true;
                    }
                        return false;
                break;
            case 'follow':
                    $likeLab=new Favorite();
                    $likeLab->refence_id=$request['refence_id'];
                    $likeLab->refence_type=$request['refence_type'];
                    $likeLab->user_id=auth()->user()->id;
                    $likeLab->is_follow='1';
                    if($likeLab->save()){
                        return true;
                    }
                    return false;
                break;
            case 'un-follow':
                $likeLab=new Favorite();
                $likeLab->refence_id=$request['refence_id'];
                $likeLab->refence_type=$request['refence_type'];
                $likeLab->user_id=auth()->user()->id;
                $likeLab->is_follow='0';
                if($likeLab->save()){
                    return true;
                }
                return false;
                break;
            case 'favorite':
                $likeLab=new Favorite();
                $likeLab->refence_id=$request['refence_id'];
                $likeLab->refence_type=$request['refence_type'];
                $likeLab->user_id=auth()->user()->id;
                $likeLab->is_favorite='0';
                if($likeLab->save()){
                    return true;
                }
                return false;
                break;
            case 'un-favorite':
                $likeLab=new Favorite();
                $likeLab->refence_id=$request['refence_id'];
                $likeLab->refence_type=$request['refence_type'];
                $likeLab->user_id=auth()->user()->id;
                $likeLab->is_favorite='1';
                if($likeLab->save()){
                    return true;
                }
                return false;
                break;
            default:
                return false;
        }
    } catch (\Exception $e){
        dd($e);
        return false;
    }   
}

public function updateLabActivity($activity,$id){
    try {
        switch ($activity){
            case 'like':
                $updateLab=Favorite::find($id);
                $updateLab->is_like="0";
                $updateLab->save();
                return true;
                break;
            case 'dislike':
                $updateLab=Favorite::find($id);
                $updateLab->is_like="0";
                $updateLab->save();
                return true;
                break;
            case 'follow':
                $updateLab=Favorite::find($id);
                $updateLab->is_follow="1";
                $updateLab->save();
                return true;
                break;
            case 'un-follow':
                $updateLab=Favorite::find($id);
                $updateLab->is_follow="0";
                $updateLab->save();
                return true;
                break;
            case 'favorite':
                
                $updateLab=Favorite::find($id);
                $updateLab->is_favorite="1";
                if($updateLab->save()){
                    return true;
                }
                return false;
                break;
            case 'un-favorite':
                $updateLab=Favorite::find($id);
                $updateLab->is_favorite="0";
                if($updateLab->save()){
                    return true;
                }
                break;
            default:
                return false;
        }
    } catch (\Exception $e){
        return false;
    }
}
}
