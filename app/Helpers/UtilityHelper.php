<?php
namespace App\Helpers;

use App\Models\Organization;
use Illuminate\Support\Str;

class UtilityHelper{
    
    public static function generateSlug($name,$model)
    {       
            $name=preg_replace('/[^A-Za-z0-9\-]/', '', $name);
            $slug=$slug_format=Str::slug($name);
            $next=1;
            while($model::where("slug",'=',$slug)->pluck("name")->first()){
                $slug="{$slug_format}-{$next}";
                $next++;
            }
            return $slug;
    }
}