<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\UserTag;

class UserTagsService
{
    public function addTags($request)
    {
        try {
            $deleteTags = UserTag::where(['user_id' => auth()->user()->id])->delete();
            $inputAllTags = $request->all();
            $allTags = [];

            foreach ($inputAllTags['tag_id'] as $key => $value) {
                $checkExistingTags = UserTag::where(['user_id' =>auth()->user()->id, 'tag_id'=>$value])->first();
                if (!$checkExistingTags) {
                    $addTag = UserTag::create([
                        'user_id'  => auth()->user()->id,
                        'tag_id'   => $value,
                    ]);
                    $allTags[] = $addTag;
                }
            }

            return $allTags;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteProfileTag($id)
    {
        try {
            $deleteTag = UserTag::where(['tag_id'=>$id, 'user_id'=>auth()->user()->id])->delete();
            if ($deleteTag) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkUserTagExists($id)
    {
        try {
            return UserTag::where(['tag_id'=>$id, 'user_id'=>auth()->user()->id])->first();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getMyTags()
    {
        try {
            $userTags = UserTag::where('user_id', auth()->user()->id)->pluck('tag_id');

            return $userTags;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
