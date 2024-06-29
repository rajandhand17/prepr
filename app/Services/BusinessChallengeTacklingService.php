<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\BusinessChallengeTackling;
use Illuminate\Support\Facades\Schema;

class BusinessChallengeTacklingService
{
    public function getBusinessChallengeTackling($request)
    {
        try {
            if ($request->language == 'en') {
                $business_challenge_tackling_list = BusinessChallengeTackling::select('id', 'title');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($request->language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('business_challenge_tacklings', $column_name)) {
                    return false;
                }
                $business_challenge_tackling_list = BusinessChallengeTackling::select('id', $column_name.' as title');
            }

            return $business_challenge_tackling_list->get();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
