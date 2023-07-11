<?php

namespace App\Services;

use App\Events\Labs\DeleteLabAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\LabSocialActivity;
use HiFolks\RandoPhp\Randomize;

class LabService
{
    public function createLab($request, $upload_cover_image)
    {
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

        $lab->status = $status;

        $lab->total_share = 0;

        $lab->is_auto_created = '0';

        $lab->is_resource_sequential = ($request->is_resource_sequential == 'yes') ? '1' : '0';
        $lab->is_sequential = ($request->is_sequential == 'yes') ? '1' : '0';
        $lab->is_achievement_enabled = ($request->is_achievement_enabled == 'yes') ? '1' : '0';
        $lab->is_notification_enabled = ($request->is_notification_enabled == 'yes') ? '1' : '0';
        $lab->is_verified = '0';
        $lab->save();

        return $lab;
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

    public function updateCoverImage($image){
        try {
            $updateCoverImage=FileUploadHelper::uploadbase64ImageToS3($image,'lab');
            if($updateCoverImage==false){
                return false;
            }
            return $updateCoverImage;
        } catch (\Exception $e){
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

    public function filterLabProgramList($labListProgram, $request)
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
                $labListProgram = $labListProgram->where('privacy', $privacy);
            }
            $labListProgram = $labListProgram->get();

            return $labListProgram;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabDetails($slug)
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
            $lab = Lab::find($lab_id)->delete();
            $associatedLabs = event(new DeleteLabAssociatedData($lab_id));
            if (!$associatedLabs) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function archieveLab($lab_id)
    {
        try {
            $lab = Lab::find($lab_id);
            $lab->status = config('constants.lab_status.archive');
            if ($lab->save()) {
                return true;
            }

            return false;
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
                return $checklabSlug;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkActivity($activity, $lab_id)
    {
        try {
            $checkActivity = LabSocialActivity::where([
                ['user_id', '=', auth()->user()->id],
                ['lab_id', '=', $lab_id],
            ]);
            switch ($activity) {
                case 'like':
                    $checkActivity = $checkActivity->where('like_dislike', '1');
                    break;
                case 'dislike':
                    $checkActivity = $checkActivity->where('like_dislike', '2');
                    break;
                case 'follow':
                    $checkActivity = $checkActivity->where('follow_unfollow', '1');
                    break;
                case 'unfollow':
                    $checkActivity = $checkActivity->where('follow_unfollow', '2');
                    break;
                case 'favourite':
                    $checkActivity = $checkActivity->where('favourite', '1');
                    break;
                case 'unfavored':
                    $checkActivity = $checkActivity->where('favourite', '2');
                    break;
                case 'share':
                    $checkActivity = $checkActivity->where('share', '1');
                    break;
                default:
                    return false;
            }

            $checkActivity = $checkActivity->first();
            if (!$checkActivity) {
                return true;
            }

            return false;
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

public function checkExistsOrNot($activity, $lab_id)
{
    try {
        $checkLabActivity = LabSocialActivity::where([
            ['user_id', '=', auth()->user()->id],
            ['lab_id', '=', $lab_id],
        ])->first();
        if ($checkLabActivity) {
            return $checkLabActivity;
        }

        return false;
    } catch (\Exception $e) {
        return false;
    }
}

public function storeLabActivity($activity, $lab_id, $request)
{
    try {
        $storeLabActivity = new LabSocialActivity();
        $storeLabActivity->user_id = auth()->user()->id;
        $storeLabActivity->lab_id = $lab_id;
        switch ($activity) {
            case 'like':
                $storeLabActivity->like_dislike = '1';
                break;
            case 'dislike':
                $storeLabActivity->like_dislike = '2';
                break;
            case 'follow':
                $storeLabActivity->follow_unfollow = '1';
                break;
            case 'unfollow':
                $storeLabActivity->follow_unfollow = '2';
                break;
            case 'favourite':
                $storeLabActivity->favourite = '1';
                break;
            case 'unfavored':
                $storeLabActivity->favourite = '2';
                break;
            default:
                return false;
        }
        if (isset($request->share) && !empty($request->share)) {
            $storeLabActivity->share = $request->share;
        }
        if ($storeLabActivity->save()) {
            return true;
        }

        return false;
    } catch (\Exception $e) {
        dd($e);

        return false;
    }
}

public function updateLabActivity($activity, $id, $request)
{
    try {
        $updateLabActivity = LabSocialActivity::find($id);
        switch ($activity) {
            case 'like':
                $updateLabActivity->like_dislike = config('constants.lab_social_activity_is_like.yes');
                break;
            case 'dislike':
                $updateLabActivity->like_dislike = config('constants.lab_social_activity_is_like.no');
                break;
            case 'follow':
                $updateLabActivity->follow_unfollow = config('constants.lab_social_activity_is_follow.yes');
                break;
            case 'unfollow':
                $updateLabActivity->follow_unfollow = config('constants.lab_social_activity_is_follow.no');
                break;
            case 'favourite':
                $updateLabActivity->favourite = config('constants.lab_social_activity_favourite.yes');
                break;
            case 'unfavored':
                $updateLabActivity->favourite = config('constants.lab_social_activity_favourite.no');
                break;
            case 'share':
                $updateLabActivity->share=config('constants.lab_social_activity_share.yes');
            break;
            default:
                return false;
                break;
        }
        if (isset($request->share) && !empty($request->share)) {
            $updateLabActivity->share = (int) $request->share;
        }
        if ($updateLabActivity->save()) {
            return true;
        }

        return false;
    } catch (\Exception $e) {
        return false;
    }
}
}
