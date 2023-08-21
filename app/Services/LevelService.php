<?php

namespace App\Services;

use App\Models\Duration;
use App\Models\Levels;
use Illuminate\Support\Facades\Schema;

class LevelService
{
    public  function  getLevels($language,$search){
        try {
            $levels=Levels::select('title','fr_CA_title');
            if($levels){
                $levels=$levels->where('title', 'like', '%'.$search.'%');;
            }
            $levels=$levels->get();
            return $levels;
        }catch(\Exception $e){
            return false;
        }
    }
}
