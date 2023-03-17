<?php
namespace App\Helpers;

use App\Models\Organization;
use Illuminate\Support\Str;

class UtilityHelper{
    
    public static function generateSlug($name)
    {   
       $list=Organization::where("slug",$name)->pluck("name")->first();
       if($list!==null){
            $slug=$slug_format=Str::slug($list);
            $next=1;
            while(Organization::where("name",'=',$slug)->pluck("name")->first()){
                $slug="{$slug_format}-{$next}";
                $next++;
            }
            return $slug;
      }else{
        $slug=Str::slug($name);
        return $slug;
      }
    }
}