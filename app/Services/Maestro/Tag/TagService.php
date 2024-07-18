<?php

namespace App\Services\Maestro\Tag;

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
            $languages = Language::where('status', 1)->get();
            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName1 = 'title';
                    $columName2 = 'tag_image';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName1 = $columName.'_title';
                    $columName2 = $columName.'_tag_image';
                }
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
            dd($e);

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
            return Tag::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createTag($request)
    {
        try {
            $categorys = json_encode($request->components);
            $category_list = str_replace(str_split('\\/!;•[]}:*?"<>|'), '', $categorys);

            $languages = Language::where('status', 1)->get();
            $tag = new Tag();
            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName1 = 'title';
                    $columName2 = 'tag_image';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName1 = $columName.'_title';
                    $columName2 = $columName.'_tag_image';
                }
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
}
