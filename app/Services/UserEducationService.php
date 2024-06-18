<?php

namespace App\Services;

use App\Helpers\MixpanelHelper;
use App\Models\CampusConnectStudentInformation;
use App\Models\UserEducation;

class UserEducationService
{
    public function addEducation($request)
    {
        try {
            $education = UserEducation::where('user_id', auth()->user()->id)->delete();
            $input = $request->all();
            $allEducation = [];
            foreach ($input['university'] as $key => $value) {
                $createEducation = UserEducation::create([
                    'user_id'    => auth()->user()->id,
                    'university' => $value,
                    'degree'     => $input['degree'][$key],
                    'start_date' => $input['start_date'][$key],
                    'end_date'   => $input['end_date'][$key],
                    'address'    => $input['address'][$key],
                    'state'      => $input['state'][$key],
                    'country'    => $input['country'][$key],
                    'description'=> $input['description'][$key],
                ]);
                $allEducation[] = $createEducation;
            }
            if ($request->enrollment_status == 'yes') {
                $records = self::addCampusConnectStudentInformation($request);
            }
            $profile_data = [
                'type' => 'education',
                'info' => $input,
            ];
            MixpanelHelper::mixpanel_tracking(config('mixpanel.update_profile'), $profile_data, auth()->user(), $request->ip());

            return $allEducation;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addCampusConnectStudentInformation($request)
    {
        try {
            $campus_info = CampusConnectStudentInformation::updateOrCreate(
                ['user_id' => auth()->user()->id],
                [
                    'user_id'             => auth()->user()->id,
                    'student_number'      => $request->student_number,
                    'current_program'     => $request->current_program,
                    'current_degree'      => $request->current_degree,
                    'current_institution' => $request->current_institution,
                    'institution_type'    => $request->institution_type,
                    'enrollment_status'   => $request->enrollment_status,
                    'current_year'        => $request->current_year,
                ]
            );

            return true;
        } catch(\Exception $e) {
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
}
