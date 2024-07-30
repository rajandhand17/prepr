<?php

namespace App\Services\Maestro;

use App\Helpers\Maestro\UtilityHelper;
use App\Models\Rank;
use App\Services\Maestro\LanguageService;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class RankService
{
    public static function getRank()
    {
        try {
            return Rank::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateRank($request, $id, $moduleMode)
    {
        try {
            if ($request->file('image')) {
                $filename = Str::random(25).'.'.$request->file('image')->getClientOriginalExtension();
                $image = Image::make($request->file('image'))->resize(735, 415)->stream();
                $img = Storage::disk('s3')->put('uploads/ranks/'.$filename, $image);
                $coverImage = 'uploads/ranks/'.$filename;
            } else {
                $coverImage = null;
            }

            if ($moduleMode === 'create') {
                $rank = new Rank();
            } else {
                $rank = Rank::find($id);
                $coverImage = !empty($coverImage) ? $coverImage : $rank->image;
            }

            $languages = LanguageService::getAllActiveLanguages();
            if (!empty($languages)) {
                foreach ($languages as $single) {
                    $columName = UtilityHelper::getColumName($single->iso, 'title');
                    $columDescriptionName = UtilityHelper::getColumName($single->iso, 'description');
                    $rank->$columDescriptionName = $request->$columDescriptionName;
                    $rank->$columName = $request->$columName;
                }
            }
            $rank->image = $coverImage;
            $rank->point = $request->point;
            $rank->status = $request->status;
            if ($rank->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findRank($id)
    {
        try {
            return Rank::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteRank($rank)
    {
        try {
            return $rank->delete();
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
                $columName = $columName.'_title';
                $ranks = Rank::select('id', $columName.' as title')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $ranks->where($columName, 'LIKE', '%'.$request->search.'%');
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
