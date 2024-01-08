<?php

namespace App\Services;

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
            return false;
        }
    }

    public static function deleteTag($id)
    {
        try {
            $deleteTag = UserTag::where('id', $id)->delete();
            if ($deleteTag) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkUserTagExists($id)
    {
        try {
            return UserTag::where('id', $id)->first();
        } catch(\Exception $e) {
            return false;
        }
    }
}
