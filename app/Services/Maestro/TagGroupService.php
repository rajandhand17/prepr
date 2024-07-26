<?php

namespace App\Services\Maestro;

use App\Helpers\Maestro\UtilityHelper;
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
            $languages = LanguageService::getAllActiveLanguages();

            foreach ($languages as $single) {
               
                    $columName1 = UtilityHelper::getColumName($single->iso,'title');
                    $columName2 = UtilityHelper::getColumName($single->iso,'description'); 
                
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
                $languages = LanguageService::getAllActiveLanguages();

                foreach ($languages as $single) {
                    $columName1 = UtilityHelper::getColumName($single->iso,'title');
                    $columName2 = UtilityHelper::getColumName($single->iso,'description'); 
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

    public static function getTagGroups()
    {
        try {
            return TagGroup::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }
}
