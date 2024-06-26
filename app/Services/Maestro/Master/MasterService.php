<?php

namespace App\Services\Maestro\Master;

use App\Models\Organization;
use App\Models\Category;
use App\Models\Skill; 
use App\Models\Lab;
use App\Models\User;
use App\Models\Levels;
use App\Models\Duration;
use App\Models\ResourceModule;
use App\Models\Rank;
use Exception;

class MasterService
{
    public static function getOrganizationsById($request)
    {
        try {
            $orgList = Organization::where(['language' => $request->language,'status' => '1'])->orderBy('id', 'DESC')->take(30);

            if ($request->search) {
                $orgList->where('title', 'LIKE', '%' . $request->search . '%');
            }
            
            $orgList = $orgList->pluck('title', 'id');
            $count = 0;
            $orgResponse = $finalResult = [];
            foreach ($orgList as $key => $orgObj) {
                $orgResponse[$count]['id'] = $key;
                $orgResponse[$count]['text'] = $orgObj;
                $count++;
            }
            $finalResult['result'] = $orgResponse;
            return response()->json($finalResult);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getCategoriesById($request)
    {
        try {
            if ($request->language == 'en') {
                $columName = 'title';
                $categories = Category::select('title as text', 'id')->orderBy('id', 'DESC')->take(30);
            } else {
                $columName = $request->language;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName.'_title';
                $categories = Category::select($columName.' as text', 'id')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $categories->where($columName, 'LIKE', '%' . $request->search . '%');
            }
            if (isset($request->component)) {
                $categories->where('components', 'like', '%'.$request->component.'%');
            }
            $categories = $categories->get();
            $jsonData['result'] = $categories;
            $jsonData['more'] = true;
            $jsonData['total_count'] = $categories->count();
            return response()->json($jsonData);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getSkillsById($request)
    {
        try {
            if ($request->language == 'en') {
                $skillsQuery = Skill::select('id', 'title')->orderBy('id', 'DESC')->take(30);
                $columName = 'title';
            } else {
                $columName = $request->language;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName . '_title';
                $skillsQuery = Skill::select('id', $columName . ' as title')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $skillsQuery->where($columName, 'LIKE', '%' . $request->search . '%');
            }
            $skillsQuery = $skillsQuery->pluck('title', 'id');
            $skillsArray = $jsonSkills = [];
            $count = 0;
            foreach ($skillsQuery as $key => $skill) {
                $skillsArray[$count]['id'] = $key;
                $skillsArray[$count]['text'] = $skill;
                $count++;
            }
            $jsonSkills['result'] = $skillsArray;
            $jsonSkills['more'] = true;
            $jsonSkills['total_count'] = $skillsQuery->count();

            return response()->json($jsonSkills);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getUsersById($request)
    {
        try {
            $userList = User::orderBy('id', 'DESC')->take(30);

            if ($request->search) {
                $userList->where('username', 'LIKE', '%' . $request->search . '%');
            }
            
            $userList = $userList->pluck('username', 'id');
            $count = 0;
            $orgResponse = $finalResult = [];
            foreach ($userList as $key => $orgObj) {
                $orgResponse[$count]['id'] = $key;
                $orgResponse[$count]['text'] = $orgObj;
                $count++;
            }
            $finalResult['result'] = $orgResponse;
            return response()->json($finalResult);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getLabsById($request)
    {
        try {
            $labs = Lab::select('id', 'title')->orderBy('id', 'DESC')->where('organization_id', $request->org_id);
            if ($request->search) {
                $labs = $labs->where('title', 'LIKE', '%' . $request->search . '%');
            }
            if ($request->privacy == 'public') {
                $labs = $labs->where('privacy', $request->privacy);
            }
            $labs = $labs->where('language', $request->language)->get()->take(20)->pluck('title', 'id');
            $count = 0;
            $json_stacks = $json_result = [];
            foreach ($labs as $key => $lab_to_return) {
                $json_stacks[$count]['id'] = $key;
                $json_stacks[$count]['text'] = $lab_to_return;
                $count++;
            }
            $json_result['result'] = $json_stacks;
            return response()->json($json_result);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getResourceModulesById($request)
    {
        try {
            $resourceOrg = ResourceModule::where(['organization_id'=> (int) $request->org_id, 'language' => $request->language])->pluck('id')->toArray();
            $resourceGlobal = ResourceModule::where(['is_global' => '1'])->pluck('id')->toArray();
            $resourceList = array_merge($resourceOrg, $resourceGlobal);
            $resourceJson = ResourceModule::whereIn('id', $resourceList)->orderBy('id', 'DESC');

            if ($request->search) {
                $resourceJson->where('title', 'LIKE', '%' .$request->search. '%');
            }
            $resourceJsons = $resourceJson->pluck('title', 'id');
            $total_count = $resourceJsons->count();
            $resourcesr = $jsonTags = [];
            $count = 0;
            foreach ($resourceJsons as $key => $tag) {
                $resourcesr[$count]['id'] = $key;
                $resourcesr[$count]['text'] = $tag;
                $count++;
            }
            $jsonTags['result'] = $resourcesr;
            $jsonTags['more'] = true;
            $jsonTags['total_count'] = $total_count;

            return response()->json($jsonTags);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getLevelsById($request)
    {
        try {
            if ($request->language == 'en') {
                $levelsQuery = Levels::select('id', 'title')->orderBy('id', 'DESC')->take(30);
                $columName = 'title';
            } else {
                $columName = $request->language;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName . '_title';
                $levelsQuery = Levels::select('id', $columName . ' as title')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $levelsQuery->where($columName, 'LIKE', '%' . $request->search . '%');
            }
            $levelsQuery = $levelsQuery->pluck('title', 'id');
            $levelsArray = $jsonLevels = [];
            $count = 0;
            foreach ($levelsQuery as $key => $level) {
                $levelsArray[$count]['id'] = $key;
                $levelsArray[$count]['text'] = $level;
                $count++;
            }
            $jsonLevels['result'] = $levelsArray;
            $jsonLevels['more'] = true;
            $jsonLevels['total_count'] = $levelsQuery->count();

            return response()->json($jsonLevels);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getDurationsById($request)
    {
        try {
            if ($request->language == 'en') {
                $durationQuery = Duration::select('id', 'title')->orderBy('id', 'DESC')->take(30);
                $columName = 'title';
            } else {
                $columName = $request->language;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName . '_title';
                $durationQuery = Duration::select('id', $columName . ' as title')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $durationQuery->where($columName, 'LIKE', '%' . $request->search . '%');
            }
            $durationQuery = $durationQuery->pluck('title', 'id');
            $durationsArray = $jsonDurations = [];
            $count = 0;
            foreach ($durationQuery as $key => $duration) {
                $durationsArray[$count]['id'] = $key;
                $durationsArray[$count]['text'] = $duration;
                $count++;
            }
            $jsonDurations['result'] = $durationsArray;
            $jsonDurations['more'] = true;
            $jsonDurations['total_count'] = $durationQuery->count();

            return response()->json($jsonDurations);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getMinRanksById($request)
    {
        try {
            if ($request->language == 'en') {
                $ranks = Rank::select('id', 'title')->orderBy('id', 'DESC')->take(30);
                $columName = 'title';
            } else {
                $columName = $request->language;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName . '_title';
                $ranks = Rank::select('id', $columName . ' as title')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $ranks->where($columName, 'LIKE', '%' . $request->search . '%');
            }
            $ranks = $ranks->pluck('title', 'id');
            $durationsArray = $jsonData = [];
            $count = 0;
            foreach ($ranks as $key => $duration) {
                $durationsArray[$count]['id'] = $key;
                $durationsArray[$count]['text'] = $duration;
                $count++;
            }
            $jsonData['result'] = $durationsArray;
            $jsonData['more'] = true;
            $jsonData['total_count'] = $ranks->count();
            return response()->json($jsonData);
        } catch (Exception $e) {
            return false;
        }
    }
}