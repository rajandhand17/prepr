<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserPersonal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserPersonalService
{
    public function addPersonalDetail($request)
    {
        try {
            $gender = config('constants.gender.decline_to_answer');
            switch ($request->gender) {
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
            $recent_immigrant = config('constants.recent_immigration.no');
            switch ($request->recent_immigrant) {
                case 'true':
                    $recent_immigrant = config('constants.recent_immigrant.yes');
                    break;
                case 'false':
                    $recent_immigrant = config('constants.recent_immigration.no');
                    break;
                default:
                    $recent_immigrant = config('constants.recent_immigration.no');
            }

            $indigenous_group = config('constants.indigenous_group.no');
            switch ($request->indigenous_group) {
                case 'true':
                    $indigenous_group = config('constants.indigenous_group.yes');
                    break;
                case 'false':
                    $indigenous_group = config('constants.indigenous_group.no');
                    break;
                default:
                    $indigenous_group = config('constants.indigenous_group.no');
            }

            $visible_minority = config('constants.visible_minority.no');
            switch ($request->visible_minority) {
                case 'true':
                    $visible_minority = config('constants.visible_minority.yes');
                    break;
                case 'false':
                    $visible_minority = config('constants.visible_minority.no');
                    break;
                default:
                    $visible_minority = config('constants.visible_minority.no');
            }
            $disability = config('constants.disability.no');
            switch ($request->disability) {
                case 'true':
                    $disability = config('constants.disability.yes');
                    break;
                case 'false':
                    $disability = config('constants.disability.no');
                    break;
                default:
                    $disability = config('constants.disability.no');
            }

            $user = auth()->user();
            $dob = new Carbon($request->date_of_birth);
            $now = Carbon::now();
            $age = $dob->diffInYears($now);
            $externalLinks = ProfileExternalLinksService::updateProfileExternalLinks($request, $user->id);
            $userPersonalDetails = UserPersonal::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'age'             => $age,
                'about'           => $request->about,
                'purpose'         => $request->purpose,
                'user_type'       => $request->user_type,
                'gender'          => $gender,
                'date_of_birth'   => $request->date_of_birth,
                'recent_immigrant'=> $recent_immigrant,
                'indigenous_group'=> $indigenous_group,
                'visible_minority'=> $visible_minority,
                'disability'      => $disability,
            ]);

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function profileImageUpload($request)
    {
        try {
            $profile = $request->file('profile_image');
            $profilePath = 'uploads/users/'.auth()->user()->id.Str::random(40).'.'.$profile->extension();
            $storeResumePath = Storage::disk('s3')->put($profilePath, file_get_contents($profile));
            $updateProfile = User::where('id', auth()->user()->id)->first();
            $updateProfile->profile_image = $profilePath;
            $updateProfile->save();

            return $updateProfile;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
