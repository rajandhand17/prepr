<?php

namespace App\Services\Maestro\Rank;

use App\Models\Language;
use App\Models\Rank;
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
            $languages = Language::where('status', 1)->get();

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
            if (!empty($languages)) {
                foreach ($languages as $single) {
                    if ($single->iso == 'en') {
                        $columName = 'title';
                        $columDescriptionName = 'description';
                    } else {
                        $columName = $single->iso;
                        $columDescriptionName = $single->iso;
                        if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                            $columName = str_replace(' ', '_', $columName);
                            $columDescriptionName = str_replace(' ', '_', $columDescriptionName);
                        }
                        if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                            $columName = str_replace('-', '_', $columName);
                            $columDescriptionName = str_replace('-', '_', $columDescriptionName);
                        }
                        $columName = $columName.'_title';
                        $columDescriptionName = $columDescriptionName.'_description';
                    }
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
}
