<?php

namespace App\Services;

use App\Models\CampusConnectStudentInformation;
use App\Models\User;
use App\Models\UserCertificate;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserPatent;
use App\Models\UserPersonal;
use App\Models\UserPersonalFile;
use App\Models\UserSkills;
use DB;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public static function getProfileBasedOnUserName($user_name)
    {
        try {
            $profile = User::where('username', $user_name)->first();
            if ($profile != null) {
                return $profile;
            }
            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addUserExperience($request)
    {
        try {
           $deleteExistingExperience=UserExperience::where('user_id',auth()->user()->id)->forceDelete();
            $userInput = $request->all();
            $input = $request->all();
            foreach ($userInput['company'] as $key=> $value){
                $userExperience=UserExperience::create(['user_id' =>auth()->user()->id,
                    'company' => $value,
                    'position' => $input['position'][$key],
                    'start_date' => $input['start_date'][$key],
                    'end_date' => $input['end_date'][$key],
                    'address' => $input['address'][$key],
                    'state' => $input['state'][$key],
                    'country' => $input['country'][$key],
                    'description' => $input['description'][$key],
                ]);
            }
            return $userExperience;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deleteUserExperience($id)
    {
        try {
            return  UserExperience::where('id', $id)->delete();
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addPersonalDetail($request)
    {
        try {
            $gender = config('constants.gender.decline_to_answer');
            switch ($request->gender){
                case 'male':
                    $gender = config('constants.gender.male');
                    break;
                case 'female':
                    $gender = config('constants.gender.female');
                    break;
                case 'other':
                    $gender = config('constants.gender.other');
                    break;
                default:
                    $gender = config('constants.gender.decline_to_answer');
                    break;
            }
            $recent_immigrant=config('constants.recent_immigration.no');
            switch ($request->recent_immigrant) {
                case 'true':
                    $recent_immigrant=config('constants.recent_immigrant.yes');
                    break;
                case 'false':
                    $recent_immigrant=config('constants.recent_immigration.no');
                    break;
                default:
                    $recent_immigrant=config('constants.recent_immigration.no');
            }

            $indigenous_group=config('constants.indigenous_group.no');
            switch ($request->indigenous_group) {
                case 'true':
                    $indigenous_group=config('constants.indigenous_group.yes');
                    break;
                case 'false':
                    $indigenous_group=config('constants.indigenous_group.no');
                    break;
                default:
                    $indigenous_group=config('constants.indigenous_group.no');
            }

            $visible_minority=config('constants.visible_minority.no');
            switch ($request->visible_minority) {
                case 'true':
                    $visible_minority=config('constants.visible_minority.yes');
                    break;
                case 'false':
                    $visible_minority=config('constants.visible_minority.no');
                    break;
                default:
                    $visible_minority=config('constants.visible_minority.no');
            }
            $disability=config('constants.disability.no');
            switch ($request->disability) {
                case 'true':
                    $disability=config('constants.disability.yes');
                    break;
                case 'false':
                    $disability=config('constants.disability.no');
                    break;
                default:
                    $disability=config('constants.disability.no');
            }

            $userPersonalDetails = UserPersonal::updateOrCreate([
                'user_id' => auth()->user()->id,
            ], [
                'age'           => $request->age,
                'about'         => $request->about,
                'purpose'       => $request->purpose,
                'user_type'     => $request->user_type,
                'gender'        => $gender,
                'date_of_birth' => $request->date_of_birth,
                'recent_immigrant'=> $recent_immigrant,
                'indigenous_group'=> $indigenous_group,
                'visible_minority'=> $visible_minority,
                'disability'      => $disability,
            ]);
            if($request->file('resume')){
                self::uploadResume($request);
            }
             return $userPersonalDetails;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function uploadResume($request){
        try {
            $resumeFile = $request->file('resume');
            $resumePath = 'uploads/personal_files/' . auth()->user()->id . '_' . $resumeFile->getClientOriginalName();
            $storeResumePath=Storage::disk('s3')->put($resumePath, file_get_contents($resumeFile));
            $storeData= UserPersonalFile::updateOrCreate(
                ['user_id' => auth()->user()->id, 'name' => $resumePath],
                [
                    'original' => $resumeFile->getClientOriginalName(),
                    'path' => 'uploads/personal_files',
                    'public' => '1'
                ]
            );

           return $storeData;
        }catch(\Exception $e){
            return false;
        }
    }
    public function addEducation($request)
    {
        try {
            $education=UserEducation::where('user_id',auth()->user()->id)->forceDelete();
            $input=$request->all();
            foreach($input['university'] as $key => $value) {
                $createAddEducation=  UserEducation::create([
                    "user_id"    => auth()->user()->id,
                    "university" => $value,
                    "degree"     =>$input['degree'][$key],
                    "start_date" =>$input['start_date'][$key],
                    "end_date"   =>$input['end_date'][$key],
                    "address"    =>$input['address'][$key],
                    "description"=>$input['description'][$key],
                ]);
            }
            if ($request->enrollment_status == 'yes') {
               $records= self::addCampusConnectStudentInformation($request);
            }
            return $createAddEducation;
        } catch(\Exception $e) {
            return false;
        }
    }
    public function addCampusConnectStudentInformation($request){
        try {
                $campus_info = CampusConnectStudentInformation::updateOrCreate(
                    ['user_id' => auth()->user()->id],
                    [
                    'user_id' => auth()->user()->id,
                    'student_number' => $request->student_number,
                    'current_program' => $request->current_program,
                    'current_degree' => $request->current_degree,
                    'current_institution' => $request->current_institution,
                    'institution_type' => $request->institution_type,
                    'enrollment_status' => $request->enrollment_status,
                    'current_year' => $request->current_year,
                ]);
                return true;
        }catch(\Exception $e) {
            return false;
        }
    }
    public static function deleteEducation($id)
    {
        try {
            return UserEducation::where('id', '=', $id)->delete();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkUserEducation($id)
    {
        try {
            return UserEducation::where('id', '=', $id)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addPatent($request)
    {
        try {
            $deleteExistingPatent=UserPatent::where('user_id', '=', auth()->user()->id)->delete();
            $input=$request->all();
            $inputData=$input;
            foreach ($inputData['title'] as $key => $value){
                $create=UserPatent::create([
                    'user_id'    => auth()->user()->id,
                    "title"      =>$value,
                    "name"       =>$input['name'][$key],
                    "patent_date"=>$input['patent_date'][$key],
                    "description"=>$input['description'][$key],
                ]);
            }
            return $create;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addSkills($request)
    {
        try {
            $deleteSKills=UserSkills::where(['user_id' => auth()->user()->id])->forceDelete();
            $inputAllSkills = $request->all();
            foreach($inputAllSkills['skill_id'] as $key => $value){
                $addSkills = UserSkills::create([
                    'user_id' => auth()->user()->id,
                    'skill'   => $value,
                ]);
            }
                return $addSkills;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addCertificate($request)
    {
        try {
            $deleteExisitingCertificate=UserCertificate::where("user_id",auth()->user()->id)->forceDelete();
            $allInputs=$request->all();
            $inputs=$request->all();
            foreach ($allInputs['company'] as $key => $value){
             $certificate=UserCertificate::create([
                    "user_id" => auth()->user()->id,
                    "company"=>$value,
                    "name"=>$inputs["name"][$key],
                    "start_date"=>$inputs["start_date"][$key],
                    "end_date"=>$inputs["end_date"][$key],
                    "description"=>$inputs["description"][$key],
                ]);
            }
            return $certificate;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteSkill($id)
    {
        try {
            $deleteSkill = UserSkills::where('id', $id)->delete();
            if ($deleteSkill) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkUserSkillDeleteExists($id)
    {
        try {
            return UserSkills::where('id', $id)->first();
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

    public static function checkUserPatent($id)
    {
        try {
            return UserPatent::where('id', $id)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteUserPatent($id)
    {
        try {
            return UserPatent::where('id', $id)->delete();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkUserCertificate($id)
    {
        try {
            return UserCertificate::where('id', $id)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteUserCertificate($id)
    {
        try {
            return UserCertificate::where('id', $id)->delete();
        } catch(\Exception $e) {
            return false;
        }
    }
}
