<?php

namespace App\Services\Public;

use App\Models\Challenge;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserAchievement;
use Dompdf\Dompdf;

class AchievementService
{
    public function getList($request)
    {
        try {
            $achievement_list = UserAchievement::select();
            $achievement_list = self::filterAchievementList($request, $achievement_list);

            return $achievement_list->paginate(config('site-settings.pagination_per_page'));
        } catch(\Exception $e) {
            return false;
        }
    }

    public function filterAchievementList($request, $achievement_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $achievement_list = $achievement_list->where('user_achievements.title', $request->search);
            }

            if ($request->has('type') && !empty($request->type)) {
                $achievement_list = $achievement_list->whereIn('user_achievements.achievement_type', $request->type);
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
                        $achievement_list->orderBy('user_achievements.created_at', 'ASC');
                        break;
                    default:
                        $achievement_list->orderBy('user_achievements.id', 'ASC');
                }
            }

            return $achievement_list;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getAchievementBasedOnCertificateNumber($certificateNumber)
    {
        try {
            return UserAchievement::where(['certificate_number' => $certificateNumber])->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public function downloadCertificate($certificateNumber, $type)
    {
        try {
            $userAchievement = UserAchievement::where('certificate_number', $certificateNumber)->first();
            if ($userAchievement) {
                $userData = User::find($userAchievement->user_id);
                $challengeDetails = $userAchievement->module_parent_id ? Challenge::find($userAchievement->module_parent_id) : null;
                $organisationDetails = isset($challengeDetails->organization_id) ? Organization::find($challengeDetails->organization_id) : null;
                $organisationName = isset($organisationDetails) ? $organisationDetails->display_name : 'Learnlab';
                $userAchievement->organisation_name = $organisationName;
                $data = [
                    'certificateNumber'         => $certificateNumber,
                    'userAchievement'           => $userAchievement,
                    'type'                      => $type,
                    'user'                      => $userData,
                    'user_id'                   => $userData->uuid,
                    'strAchievementName'        => $userAchievement->title,
                ];
                $dompdf = new Dompdf();
                $html = view('PDF.achievement_certificate', $data)->render();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('legal', 'landscape');
                $dompdf->render();
                $dompdf->stream($userAchievement->certificate_number.'.pdf');
                exit;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }
}
