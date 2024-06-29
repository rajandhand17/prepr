<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\UserPatent;

class UserPatentService
{
    public function addPatent($request)
    {
        try {
            $deleteExistingPatent = UserPatent::where('user_id', '=', auth()->user()->id)->delete();
            $input = $request->all();
            $allPatents = [];
            foreach ($input['company'] as $key => $value) {
                $create = UserPatent::create([
                    'user_id'    => auth()->user()->id,
                    'title'      => $value,
                    'name'       => $input['name'][$key],
                    'patent_date'=> $input['patent_date'][$key],
                    'description'=> $input['description'][$key],
                ]);
                $allPatents[] = $create;
            }

            return $allPatents;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteUserPatent($id)
    {
        try {
            return UserPatent::where('id', $id)->delete();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function checkUserPatent($id)
    {
        try {
            return UserPatent::where('id', $id)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
