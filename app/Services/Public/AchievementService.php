<?php

namespace App\Services\Public;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserAchievement;
use Dompdf\Dompdf;
use Exception;
use Spatie\PdfToImage\Pdf;

class AchievementService
{
    public function getList($request)
    {
        try {
            $achievement_list = UserAchievement::select()->where('user_id', auth()->user()->id);
            $achievement_list = self::filterAchievementList($request, $achievement_list);

            return $achievement_list->paginate(config('site-settings.pagination_per_page'));
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getAchievementList($userId, $request)
    {
        try {
            $achievement_list = UserAchievement::select()->where('user_id', $userId);
            $achievement_list = self::filterAchievementList($request, $achievement_list);

            return $achievement_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function filterAchievementList($request, $achievement_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $achievement_list = $achievement_list->where('user_achievements.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('type') && !empty($request->type)) {
                $typesMapping = [
                    'lab'            => '0',
                    'lab-program'    => '1',
                    'challenge'      => ['9', '10'],
                    'challenge-path' => '3',
                    'resource-group' => '4',
                    'appreciation'   => '5',
                    'activity'       => '6',
                    'skill-activity' => '7',
                    'imported'       => '8',
                    'winner'         => '9',
                    'participation'  => '10',
                ];
                $achievementTypeMap = array_map(function ($type) use ($typesMapping) {
                    return $typesMapping[$type] ?? null;
                }, $request->type);

                $achievementType = array_reduce($achievementTypeMap, function ($carry, $item) {
                    return is_array($item) ? array_merge($carry, $item) : array_merge($carry, [$item]);
                }, []);

                $achievement_list = $achievement_list->whereIn('user_achievements.achievement_type', $achievementType);
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $achievement_list->orderBy('user_achievements.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $achievement_list->orderBy('user_achievements.title', 'DESC');
                        break;
                    case 'creation_date':
                        $achievement_list->orderBy('user_achievements.issue_date', 'ASC');
                        break;
                    default:
                        $achievement_list->orderBy('user_achievements.id', 'ASC');
                }
            }

            return $achievement_list;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getAchievementBasedOnCertificateNumber($certificate_id)
    {
        try {
            return UserAchievement::where(['certificate_number' => $certificate_id])->first();
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function downloadCertificate($certificate_id, $format)
    {
        try {
            $userAchievement = UserAchievement::where('certificate_number', $certificate_id)->first();
            if ($userAchievement) {
                $userData = User::find($userAchievement->user_id);
                $challengeDetails = $userAchievement->module_parent_id ? Challenge::find($userAchievement->module_parent_id) : null;
                $organisationDetails = isset($challengeDetails->organization_id) ? Organization::find($challengeDetails->organization_id) : null;
                $organisationName = isset($organisationDetails) ? $organisationDetails->display_name : 'Learnlab';
                $userAchievement->organisation_name = $organisationName;
                $data = [
                    'certificateNumber'         => $certificate_id,
                    'userAchievement'           => $userAchievement,
                    'type'                      => $userAchievement->achievement_type,
                    'user'                      => $userData,
                    'user_id'                   => $userData->uuid,
                    'strAchievementName'        => $userAchievement->title,
                ];
                $dompdf = new Dompdf();
                $html = view('PDF.achievement_certificate', $data)->render();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('legal', 'landscape');
                $dompdf->render();
                $pdfPath = storage_path('app/certificate/'.$userAchievement->certificate_number.'.pdf');
                if ($format === 'image') {
                    file_put_contents($pdfPath, $dompdf->output());
                    $pdf = new Pdf($pdfPath);
                    $pdf->setOutputFormat('jpeg');
                    $imagePath = storage_path('app/certificate/'.$userAchievement->certificate_number.'.jpeg');
                    $pdf->saveImage($imagePath);
                    $fileName = $userAchievement->certificate_number.'.jpeg';
                    $s3BackUrl = FileUploadHelper::uploadLocalStorageImageToS3(response()->download($imagePath), 'certificate');
                } elseif ($format === 'pdf') {
                    file_put_contents($pdfPath, $dompdf->output());
                    $s3BackUrl = FileUploadHelper::uploadLocalStoragePDFToS3(response()->download($pdfPath), 'certificate');
                }

                return config('site-settings.aws_url').$s3BackUrl;
            }

            return false;
        } catch(Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getColumnValue($request)
    {
        try {
            $value = null;
            switch ($request->is_featured) {
                case 'no':
                    $value = '0';
                    break;
                case 'yes':
                    $value = '1';
                    break;
                default:
                    $value = null;
                    break;
            }
            if ($value != null) {
                return ['action' => $value];
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkachievementActivity($certificate_id, $action)
    {
        try {
            $checkActivity = UserAchievement::where(
                [
                    'certificate_number'    => $certificate_id,
                    'is_featured'           => $action,
                ]
            )->first();
            if ($checkActivity != null) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function achievementActivity($certificate_id, $action)
    {
        try {
            $userAchievement = UserAchievement::where('certificate_number', $certificate_id)->first();
            if ($userAchievement) {
                $userAchievement->is_featured = $action;
                $userAchievement->save();

                return $userAchievement;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
