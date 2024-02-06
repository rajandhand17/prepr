<?php

namespace App\Services;

use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserPersonalFile;
use Illuminate\Support\Facades\Storage;

class UserExperienceService
{
    public function addExperience($request)
    {
        try {
            $deleteExistingExperience = UserExperience::where('user_id', auth()->user()->id)->delete();
            $input = $request->all();
            $insertRecords = [];
            foreach ($input['company'] as $key=> $value) {
                $userExperience = UserExperience::create(['user_id' => auth()->user()->id,
                    'company'                                       => $value,
                    'position'                                      => $input['position'][$key],
                    'start_date'                                    => $input['start_date'][$key],
                    'end_date'                                      => $input['end_date'][$key],
                    'address'                                       => $input['address'][$key],
                    'state'                                         => $input['state'][$key],
                    'country'                                       => $input['country'][$key],
                    'description'                                   => $input['description'][$key],
                ]);
                $insertRecords[] = $userExperience;
            }

            return $insertRecords;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deleteExperience($id)
    {
        try {
            return  UserExperience::where('id', $id)->delete();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkUserExperience($id)
    {
        try {
            return UserExperience::where('id', $id)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function fileUpload($request)
    {
        try {
            $resumeFile = $request->file('file');
            $resumePath = 'uploads/personal_files/'.auth()->user()->id.'_'.$resumeFile->getClientOriginalName();
            $storeResumePath = Storage::disk('s3')->put($resumePath, file_get_contents($resumeFile));
            $storeData = UserPersonalFile::updateOrCreate(
                ['user_id' => auth()->user()->id, 'name' => $resumePath],
                [
                    'original' => $resumeFile->getClientOriginalName(),
                    'path'     => 'uploads/personal_files',
                    'public'   => '1',
                ]
            );

            return $storeData;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteUserExperienceBasedOnUserId($userId){
        try {
            $getUserExperienceId=UserExperience::where('user_id',$userId)->pluck('id');
            if($getUserExperienceId->isNotEmpty()){
                $deleteUserExperience=UserExperience::whereIn('id',$getUserExperienceId)->delete();
                if(!$deleteUserExperience){
                    return false;
                }
                return true;
            }
            return true;
        }catch (\Exception $e) {
            return false;
        }
    }
}
