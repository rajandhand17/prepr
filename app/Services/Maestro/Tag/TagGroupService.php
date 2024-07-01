<?php

namespace App\Services\Maestro\Tag;

use App\Models\Language;
use App\Models\Tag;
use App\Models\TagGroup;
use Exception;

class TagGroupService
{
    public static function getTagGroupById($id)
    {
        try {
            $tagGroup = TagGroup::find($id);
            if ($tagGroup != null) {
                return $tagGroup;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateTagGroupById($id, $request)
    {
        try {
            $input = $request->all();
            $tagGroup = TagGroup::find($id);
            if ($request->title !== $tagGroup->title && TagGroup::where('title', $request->title)->count() > 0) {
                return redirect()->route('taggroup.index')->with(['error' => 'Tag Group title already exists']);
            }
            $languages = Language::where('status', 1)->get();

            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName1 = 'title';
                    $columName2 = 'description';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName1 = $columName.'_title';
                    $columName2 = $columName.'_description';
                }
                $tagGroup->$columName1 = $request->$columName1;
                $tagGroup->$columName2 = $request->$columName2;
            }
            $tagGroup->tags = $request->tags;
            $tagGroup->save();

            return true;
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    public static function deleteTagGroupById($id)
    {
        try {
            $tagGroup = TagGroup::find($id);
            if (!empty($tagGroup)) {
                return $tagGroup->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createTagGroup($request)
    {
        try {
            if (!empty($request->title)) {
                $input = $request->all();

                if (TagGroup::where('title', $request->title)->count() > 0) {
                    return redirect()->route('taggroup.index')->with(['error' => 'Tag Group title already exists']);
                }
                $tagGroup = new TagGroup();

                $languages = Language::where('status', 1)->get();

                foreach ($languages as $single) {
                    if ($single->iso == 'en') {
                        $columName1 = 'title';
                        $columName2 = 'description';
                    } else {
                        $columName = $single->iso;
                        if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                            $columName = str_replace(' ', '_', $columName);
                        }
                        if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                            $columName = str_replace('-', '_', $columName);
                        }
                        $columName1 = $columName.'_title';
                        $columName2 = $columName.'_description';
                    }
                    $tagGroup->$columName1 = $request->$columName1;
                    $tagGroup->$columName2 = $request->$columName2;
                }
                $tagGroup->tags = $request->tags;
                $tagGroup->save();

                return redirect()->route('taggroup.index')->with('success', 'Tag Group added successfully');
            }

            return redirect()->with('error', 'Enter Tag Group');
        } catch (Exception $e) {
            return redirect()->route('taggroup.index')->with(['error' => $e->getMessage()]);
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
}
