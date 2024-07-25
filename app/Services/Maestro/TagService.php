<?php

namespace App\Services\Maestro;

use App\Helpers\Maestro\UtilityHelper;
use App\Models\Language;
use App\Models\Tag;
use Exception;

class TagService
{
    public static function getTagById($id)
    {
        try {
            $tag = Tag::find($id);
            if ($tag != null) {
                return $tag;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateTagById($id, $request)
    {
        try {
            $tag = Tag::find($id);
            $categorys = json_encode($request->components);
            $category_list = str_replace(str_split('\\/!;•[]}:*?"<>|'), '', $categorys);
                $languages = LanguageService::getAllActiveLanguages();
            foreach ($languages as $single) {
                $columName1 = UtilityHelper::getColumName($single->iso,'title');
                $columName2 = UtilityHelper::getColumName($single->iso,'tag_image'); 
                
                $tag->$columName1 = $request->$columName1;
                $tag_image = '';
                if ($request->file($columName2)) {
                    $tag_image = $request->file($columName2)->store('uploads/tag', 's3');
                    $tag->$columName2 = $tag_image;
                }
            }
            $tag->components = $category_list;
            $tag->created_at = date('Y-m-d H:i:s');
            $tag->updated_at = date('Y-m-d H:i:s');
            $tag->save();

            return redirect()->route('tags.index')->with('success', 'Tag update successfully');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteTag($id)
    {
        try {
            $tag = Tag::find($id);
            if (!empty($tag)) {
                return $tag->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getTags()
    {
        try {
            return Tag::orderBy('id', 'desc')->pluck('title', 'id');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createTag($request)
    {
        try {
            $categorys = json_encode($request->components);
            $category_list = str_replace(str_split('\\/!;•[]}:*?"<>|'), '', $categorys);

            $languages = LanguageService::getAllActiveLanguages();
            $tag = new Tag();
            foreach ($languages as $single) {
                $columName1 = UtilityHelper::getColumName($single->iso,'title');
                $columName2 = UtilityHelper::getColumName($single->iso,'tag_image'); 
                
                $tag->$columName1 = $request->$columName1;

                $tag_image = '';
                if ($request->file($columName2)) {
                    $tag_image = $request->file($columName2)->store('uploads/tag', 's3');
                }
                $tag->$columName2 = $tag_image;
            }

            $tag->components = $category_list;
            $tag->created_at = date('Y-m-d H:i:s');
            $tag->updated_at = date('Y-m-d H:i:s');
            $tag->save();

            return redirect()->route('tags.index')->with('success', 'Tag added successfully');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getSelectedTagByIds($tags)
    {
        try {
            $selectedTags = [];
            foreach ($tags as $tag_id) {
                if (Tag::where('id', $tag_id)->get()->count() > 0) {
                    $tag_names[] = Tag::find($tag_id)->title;
                } else {
                    $selectedTags = "Tag doesn't exist";
                }
            }
            $selectedTags = implode(', ', $tag_names);
            return $selectedTags;
        } catch (Exception $e) {
            return false;
        }
    }
   
}
