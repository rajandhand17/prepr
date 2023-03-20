<?php
namespace App\Helpers;

use App\Models\Organization;
use Illuminate\Support\Str;

class UtilityHelper{
    
    public static function generateSlug($name,$model)
    {   
      //  if($model=="organization"){
      //      $model=new Organization();
      //  }
            $slug=$slug_format=Str::slug($name);
            $next=1;
            while($model::where("name",'=',$slug)->pluck("name")->first()){
                $slug="{$slug_format}-{$next}";
                $next++;
            }
            return $slug;
      
    }
}