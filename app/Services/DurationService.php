<?php

namespace App\Services;

use App\Models\Duration;
use Illuminate\Support\Facades\Schema;

class DurationService
{
  public  function getDuration($language = 'en', $search = null)
  {
      try {
        $duration =Duration::select('title','fr_CA_title');
        if($duration){
            $duration=$duration->where('title', 'like', '%'.$search.'%');;
        }
        $duration=$duration->get();
        return $duration;
      }catch(\Exception $e) {
          return false;
      }
  }

}
