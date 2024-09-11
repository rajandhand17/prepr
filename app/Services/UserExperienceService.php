<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Jobs\MixPenalJob;
use App\Models\UserExperience;
use App\Models\UserPersonalFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserExperienceService
{
    public function addExperience($request)
    {
        try {
            $deleteExistingExperience = UserExperience::where('user_id', auth()->user()->id)->delete();
            $input = $request->all();
            $insertRecords = [];
            foreach ($input['company'] as $key => $value) {
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
            $profile_data = [
                'type' => 'experience',
                'info' => $input,
            ];
            MixPenalJob::dispatch(config('mixpanel.update_profile'), $profile_data, auth()->user(), $request->ip());

            return $insertRecords;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteExperience($id)
    {
        try {
            return UserExperience::where('id', $id)->delete();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkUserExperience($id)
    {
        try {
            return UserExperience::where('id', $id)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkUserExperienceBasedOnTitle($companyName)
    {
        try {
            return UserExperience::where(['user_id' => auth()->user()->id, 'company' => $companyName])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteFile($id)
    {
        try {
            $deleteFile = UserPersonalFile::query()->where('id', $id)->delete();
            if ($deleteFile) {
                return true;
            }

            return false;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function updateFilePrivacy($id)
    {
        try {
            $isPublic = UserPersonalFile::where('id', $id)->first();
            if ($isPublic->public == 1) {
                UserPersonalFile::where('id', $id)->update(['public' => 0]);
            } else {
                UserPersonalFile::where('id', $id)->update(['public' => 1]);
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addUserExperienceByUsingResumeData($response, $user)
    {
        try {
            if (isset($response['data']['employer']) && $response['data']['employer'] !== null) {
                foreach ($response['data']['employer'] as $key => $value) {
                    if ($value && isset($value['company_name']) && isset($value['role'])) {
                        $startDate = isset($value['from_year'], $value['from_month'])
                            ? date('Y-m-d', strtotime($value['from_year'].'-'.$value['from_month'].'-01'))
                            : now()->toDateString();

                        $endDate = isset($value['to_year'], $value['to_month'])
                            ? date('Y-m-d', strtotime($value['to_year'].'-'.$value['to_month'].'-01'))
                            : now()->toDateString();
                        $companyName = trim(str_replace('&nbsp;', ' ', strip_tags($value['company_name'])));
                        $checkUserExperience = self::checkUserExperienceBasedOnTitle($companyName);
                        if ($checkUserExperience == null) {
                            UserExperience::create([
                                'user_id'     => $user->id,
                                'company'     => trim(str_replace('&nbsp;', ' ', strip_tags($value['company_name']))),
                                'position'    => trim(str_replace('&nbsp;', ' ', strip_tags($value['role']))),
                                'start_date'  => $startDate,
                                'end_date'    => $endDate,
                                'country'     => '',
                                'state'       => '',
                                'description' => trim(str_replace('&nbsp;', ' ', strip_tags($value['description']))),
                            ]);
                        }
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addMagnetUserExperience($user, $data)
    {
        try {
            DB::beginTransaction();
            UserExperience::query()->where('user_id', $user->id)->forceDelete();
            foreach ($data as $item) {
                UserExperience::create([
                    'user_id'  => $user->id,
                    'company'  => data_get($item, 'company', '-'),
                    'position' => data_get($item, 'job_title', '-'),
                ]);
            }
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            DB::rollBack();

            return false;
        }
    }
}
