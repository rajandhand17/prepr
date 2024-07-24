<?php

namespace App\Helpers;

use App\Mail\EmailSummaryChallengeMail;
use App\Mail\EmailSummaryLabMail;
use App\Mail\EmailSummaryNetworkMail;
use App\Models\Challenge;
use App\Models\ChallengeAchievement;
use App\Models\ChallengeSkillsGroupsStack;
use App\Models\ChallengeSocialActivity;
use App\Models\Duration;
use App\Models\Lab;
use App\Models\LabAcheivement;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabSocialActivity;
use App\Models\Language;
use App\Models\Levels;
use App\Models\ModuleCompletionStatus;
use App\Models\Project;
use App\Models\ResourceModule;
use App\Models\UserAchievement;
use App\Models\UserSkills;
use App\Services\Public\ChallengeService;
use App\Services\Public\LabService;
use App\Services\Public\ResourceModuleService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class EmailSummaryHelper
{
    public static function fetchLangaugeISO($languageISO)
    {
        try {
            $language = Language::select('iso')->where('iso', $languageISO)->first();
            if (!empty($language)) {
                return $language['iso'];
            }

            return 'en';
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function networkEmailSummary($userData, $summeryType, $summaryContent)
    {
        try {
            $summaryData['summary_date'] = self::getSummaryDate($summeryType);
            $summaryData['recently_added'] = self::getModuleListingDataComponent($summeryType, $userData, 'recently_added', $summaryData['summary_date'], 'network');
            $summaryData['most_interacted'] = self::getModuleListingDataComponent($summeryType, $userData, 'most_interacted', $summaryData['summary_date'], 'network');
            $summaryData['most_interested'] = self::getModuleListingDataComponent($summeryType, $userData, 'most_interested', $summaryData['summary_date'], 'network');
            $summaryData['completed_module_counts'] = self::getCompletedModuleCount($summeryType, $userData, $summaryData['summary_date']);
            $summaryData['top_achievements'] = self::getTopAchievements($summeryType, $userData, 'network');

            Mail::to(trim($userData->email))->send(new EmailSummaryNetworkMail($summaryData, $summaryContent, $summeryType, $userData));

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function labEmailSubscription($userData, $summeryType, $summeryContent)
    {
        try {
            $summaryData['summary_date'] = self::getSummaryDate($summeryType);
            $summaryData['most_interacted'] = self::getModuleListingDataComponent($summeryType, $userData, 'most_interacted', $summaryData['summary_date'], 'lab');
            $summaryData['most_interested'] = self::getModuleListingDataComponent($summeryType, $userData, 'most_interested', $summaryData['summary_date'], 'lab');
            $summaryData['completed_module_counts'] = self::getCompletedModuleCount($summeryType, $userData, $summaryData['summary_date']);
            $summaryData['top_achievements'] = self::getTopAchievements($summeryType, $userData, 'lab');

            Mail::to($userData->email)->send(new EmailSummaryLabMail($summaryData, $summeryContent, $summeryType, $userData));

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function challengeEmailSubscription($userData, $summeryType, $summeryContent)
    {
        try {
            $summaryData['summary_date'] = EmailSummaryHelper::getSummaryDate($summeryType);
            $summaryData['most_interacted'] = EmailSummaryHelper::getModuleListingDataComponent($summeryType, $userData, 'most_interacted', $summaryData['summary_date'], 'challenge');
            $summaryData['most_interested'] = EmailSummaryHelper::getModuleListingDataComponent($summeryType, $userData, 'most_interested', $summaryData['summary_date'], 'challenge');
            $summaryData['completed_module_counts'] = EmailSummaryHelper::getCompletedModuleCount($summeryType, $userData, $summaryData['summary_date']);
            $summaryData['top_achievements'] = EmailSummaryHelper::getTopAchievements($summeryType, $userData, 'challenge');

            Mail::to($userData->email)->send(new EmailSummaryChallengeMail($summaryData, $summeryContent, $summeryType, $userData));

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
    public static function getSummaryDate($type)
    {
        try {
            if ($type == 'weekly') {
                $now = Carbon::now();
                $from = $now->startOfWeek()->format('Y-m-d H:i');
                $to = $now->endOfWeek()->format('Y-m-d H:i');
            } else {
                $fromDate = Carbon::today()->startOfMonth();
                $from = Carbon::today()->startOfMonth()->format('Y-m-d H:i');
                $to = $fromDate->copy()->endOfMonth()->format('Y-m-d H:i');
            }
            return ['from' => $from, 'to' => $to, 'type' => $type];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getModuleListingDataComponent($summeryType, $user, $sectionType, $summary_date, $summaryModuleType)
    {
        try {
            $challenges = ChallengeService::getAll();
            $labs =  LabService::getAll();
            $resource_modules =  ResourceModuleService::getAll();

            switch ($sectionType) {
                case 'recently_added':
                    $challenges = $challenges->whereBetween('created_at', [strtotime($summary_date['from']), strtotime($summary_date['to'])]);
                    $labs = $labs->whereBetween('created_at', [strtotime($summary_date['from']), strtotime($summary_date['to'])]);
                    $resources = $resource_modules->whereBetween('created_at', [Carbon::parse($summary_date['from'])->format('Y-m-d'), Carbon::parse($summary_date['to'])->format('Y-m-d')]);
                    break;

                case 'most_interacted':
                    if ($summaryModuleType == 'network') {
                        $labData = LabSocialActivity::where(function ($query) use ($user) {
                            $query->where('user_id', $user->id)
                                    ->where('favourite', '1')
                                    ->orWhere('like_dislike', '1');
                        })->orderBy('updated_at', 'desc')->limit(3)->get()->map(function ($item) {
                            $item->source = 'lab';
                            return $item;
                        });

                        $challengeData = ChallengeSocialActivity::where(function ($query) use ($user) {
                            $query->where('user_id', $user->id)
                                    ->where('favourite', '1')
                                    ->orWhere('like_dislike', '1');
                        })->orderBy('updated_at', 'desc')->limit(3)->get()->map(function ($item) {
                            $item->source = 'challenge';
                            return $item;
                        });

                        // Combine and sort the data
                        $combinedData = $labData->merge($challengeData)
                        ->sortByDesc('updated_at')
                        ->take(3);

                        // If you need a collection instead of an array
                        $interactedModules = new Collection($combinedData->values());


                    } elseif ($summaryModuleType == 'lab') {
                        $interactedModules = LabSocialActivity::where(function ($query) use ($user) {
                            $query->where('user_id', $user->id)
                                ->where('favourite', '1')
                                ->orWhere('like_dislike', '1');
                        })->orderBy('updated_at', 'desc')->limit(3)->get();
                    } elseif ($summaryModuleType == 'challenge') {
                        $interactedModules = ChallengeSocialActivity::where(function ($query) use ($user) {
                            $query->where('user_id', $user->id)
                                ->where('favourite', '1')
                                ->orWhere('like_dislike', '1');
                        })->orderBy('updated_at', 'desc')->limit(3)->get();
                    }

                    $challengeInteractedIds = [];
                    $labInteractedIds = [];
                    if (!empty($interactedModules)) {
                        foreach ($interactedModules as $module) {
                            if ($module->source == 'challenge') {
                                $challengeInteractedIds[] = $module->challenge_id;
                            } elseif ($module->source == 'lab') {
                                $labInteractedIds[] = $module->lab_id;
                            }
                        }
                    }

                    if (!empty($challengeInteractedIds)) {
                        $challenges = $challenges->whereIn('id', $challengeInteractedIds);
                    }

                    if (!empty($labInteractedIds)) {
                        $labs = $labs->whereIn('id', $labInteractedIds);
                    }

                    break;

                case 'most_interested':
                    $userSkills = UserSkills::where('user_id', $user->id)->whereNotNull('skill')->pluck('skill');
                    $challengeInterestedIds = [];
                    $labInterestedIds = [];
                    $resourceModuleInterestedIds = [];

                    if (!empty($userSkills)) {
                        foreach ($userSkills as $skill) {
                            $challengeIdsSkills = ChallengeSkillsGroupsStack::where(['foreign_id' => $skill, 'type' => '0'])->pluck('challenge_id');
                            if (!empty($challengeIdsSkills)) {
                                foreach ($challengeIdsSkills as $challengeId) {
                                    $challengeInterestedIds[] = $challengeId;
                                }
                            }

                            $labIdsSkills = LabSkillsGroupsStack::where(['foreign_id' => $skill, 'type' => '0'])->pluck('lab_id');
                            if (!empty($labIdsSkills)) {
                                foreach ($labIdsSkills as $labId) {
                                    $labInterestedIds[] = $labId;
                                }
                            }

                            $resourceModuleIdsSkills = LabSkillsGroupsStack::where(['foreign_id' => $skill, 'type' => '0'])->pluck('lab_id');
                            if (!empty($resourceModuleIdsSkills)) {
                                foreach ($resourceModuleIdsSkills as $resourceModuleId) {
                                    $resourceModuleInterestedIds[] = $resourceModuleId;
                                }
                            }
                        }
                    }

                    $challenges = $challenges->whereIn('id', $challengeInterestedIds);
                    $labs = $labs->whereIn('id', $labInterestedIds);
                    $resource_modules = $resource_modules->whereIn('id', $resourceModuleInterestedIds);

                    break;
            }

            $summaryData = [];

            if ($summaryModuleType == 'network') {
                if ($sectionType == 'most_interacted') {
                    $resource_modules =  $resource_modules->limit(0);
                    $labs =  $labs->limit(count($labInteractedIds));
                    $challenges =  $challenges->limit(count($challengeInteractedIds));
                } else {
                    $challenges =  $challenges->limit(1);
                    $resource_modules =  $resource_modules->limit(1);
                    $labs =  $labs->limit(1);
                }
            } elseif ($summaryModuleType == 'lab') {
                $challenges =  $challenges->limit(0);
                $labs =  $labs->limit(3);
                $resource_modules =  $resource_modules->limit(0);
            } elseif ($summaryModuleType == 'challenge') {
                $challenges =  $challenges->limit(3);
                $labs =  $labs->limit(0);
                $resource_modules =  $resource_modules->limit(0);
            }

            if ($sectionType == 'most_interacted') {
                $challenges =  $challenges->get();
                $labs =  $labs->get();
                $resource_modules =  $resource_modules->get();
            } else {
                $challenges =  $challenges->orderBy('id', 'desc')->get();
                $labs =  $labs->orderBy('id', 'desc')->get();
                $resource_modules =  $resource_modules->orderBy('id', 'desc')->get();
            }

            if (!empty($challenges)) {
                foreach ($challenges as $key => $challenge) {
                    if ($challenge->media_type == 'image') {
                        $coverImage = !empty($challenge->media) ? $challenge->media : config('site-settings.cdn_url') . config('site-settings.default_challenge_cover_image');
                    } else {
                        $coverImage = config('site-settings.cdn_url') . config('site-settings.default_challenge_cover_image');
                    }

                    $is_earnable = 'yes';
                    $achievementName = null;
                    if ($sectionType == 'most_interacted') {
                        $challengeAchievement = ChallengeAchievement::where('challenge_id', $challenge->id)->first();
                        if (!empty($challengeAchievement)) {
                            $userAchievement = UserAchievement::whereIn('achievement_type', ['9', '10'])->where(['user_id' => $user->id, 'module_id' => $challenge->id])->first();
                            if (!empty($userAchievement)) {
                                $is_earnable = 'no';
                                $achievementName = !empty($userAchievement->title) ?  $userAchievement->title : $challenge->title .' , '.$userAchievement->achievement_points;
                            } else {
                                $is_earnable = 'yes';
                                $achievementName = !empty($challengeAchievement->achievement_name) ?  $challengeAchievement->achievement_name : $challenge->title .' , '.$challengeAchievement->achievement_points;
                            }
                        }

                        $challengeDeadline = 'None';
                        if ($challenge->challenge_timelines == '1') {
                            $challengeDeadline = Carbon::parse($challenge->challenge_timelines->submission_deadline_date)->format('M d, Y');
                        }

                        $challengeDuration['title'] = 'None';
                        if ($challenge->duration_id) {
                            $challengeDuration = Duration::select('title')->where('id', $challenge->duration_id)->first();
                        }

                        $challengeLevel['title'] = 'None';
                        if ($challenge->level_id) {
                            $challengeLevel = Levels::select('title')->where('id', $challenge->level_id)->first();
                        }

                        $challengeStatus = 'None';
                        switch ($challenge->is_open) {
                            case '0':
                                $challengeStatus = 'open';
                                break;
                            case '1':
                                $challengeStatus = 'closed';
                                break;
                            case '2':
                                $challengeStatus = 'completed';
                                break;
                        }
                        $summaryData[$key]['id'] = $key + 1;
                        $summaryData[$key]['module'] = 'challenge';
                        $summaryData[$key]['module_id'] = $challenge->id;
                        $summaryData[$key]['title'] = $challenge->title;
                        $summaryData[$key]['mediaType'] = $challenge->media_type;
                        $summaryData[$key]['cover_image'] = $coverImage;
                        $summaryData[$key]['deadline'] = $challengeDeadline;
                        $summaryData[$key]['duration'] = $challengeDuration['title'];
                        $summaryData[$key]['level'] = $challengeLevel['title'];
                        $summaryData[$key]['updated_at'] = Carbon::parse($challenge->updated_at)->diffForHumans();
                        $summaryData[$key]['members'] = $challenge->members()->count();
                        $summaryData[$key]['tsubmition'] = Project::select('challenge_id')->where(['challenge_id' => $challenge->id, 'is_submitted' => '1'])->get()->count();
                        $summaryData[$key]['status'] = $challengeStatus;
                        $summaryData[$key]['achievementname'] = $achievementName;
                        $summaryData[$key]['is_earnable'] = $is_earnable;
                    }
                }
            }

            if (!empty($labs)) {
                foreach ($labs as $key => $lab) {
                    $countdata = count($summaryData);

                    if ($lab->media_type == 'media_type') {
                        $coverImage = !empty($lab->media) ? $lab->media : config('site-settings.cdn_url') . config('site-settings.default_lab_cover_image');
                    } else {
                        $coverImage = config('site-settings.cdn_url') . config('site-settings.default_lab_cover_image');
                    }

                    $is_earnable = 'yes';
                    $achievementName = null;
                    if ($sectionType == 'most_interacted') {
                        $labAchievement = LabAcheivement::where('lab_id', $lab->id)->first();
                        if (!empty($labAchievement)) {
                            $userAchievement = UserAchievement::where(['achievement_type' => '0', 'user_id' => $user->id, 'module_id' => $challenge->id])->first();
                            if (!empty($userAchievement)) {
                                $is_earnable = 'no';
                                $achievementName = !empty($userAchievement->title) ?  $userAchievement->title : $lab->title . ' , ' . $userAchievement->achievement_points;
                            } else {
                                $is_earnable = 'yes';
                                $achievementName = !empty($labAchievement->achievement_name) ?  $labAchievement->achievement_name : $lab->title . ' , ' . $labAchievement->achievement_points;
                            }
                        }
                    }

                    $labDuration['title'] = 'None';
                    if ($lab->duration_id) {
                        $labDuration = Duration::select('title')->where('id', $lab->duration_id)->first();
                    }

                    $labLevel['title'] = 'None';
                    if ($lab->level_id) {
                        $labLevel = Levels::select('title')->where('id', $lab->level_id)->first();
                    }

                    $summaryData[$countdata]['id'] = $key + 1;
                    $summaryData[$countdata]['module'] = 'lab';
                    $summaryData[$countdata]['module_id'] = $lab->id;
                    $summaryData[$countdata]['title'] = $lab->title;
                    $summaryData[$countdata]['mediaType'] = $lab->mediaType;
                    $summaryData[$countdata]['cover_image'] = $coverImage;
                    $summaryData[$countdata]['deadline'] = 'none';
                    $summaryData[$countdata]['duration'] = $labDuration['title'];
                    $summaryData[$countdata]['level'] = $labLevel['title'];
                    $summaryData[$countdata]['updated_at'] = Carbon::parse($lab->updated_at)->diffForHumans();
                    $summaryData[$countdata]['members'] = $lab->members()->count();
                    $summaryData[$countdata]['tsubmition'] = 0;
                    $summaryData[$countdata]['status'] = 'Open';
                    $summaryData[$countdata]['achievementname'] = $achievementName;
                    $summaryData[$countdata]['is_earnable'] = $is_earnable;
                }
            }

            if (!empty($resource_modules)) {
                foreach ($resource_modules as $key => $resource_module) {
                    $countdata = count($summaryData);

                    if ($resource_module->media_type == 'image') {
                        $coverImage = !empty($resource_module->media) ? $resource_module->media : config('site-settings.cdn_url') . config('site-settings.default_resource_module_cover_image');
                    } else {
                        $coverImage = config('site-settings.cdn_url') . config('site-settings.default_resource_module_cover_image');
                    }

                    $resourceModuleDuration['title'] = 'None';
                    if ($resource_module->duration_id) {
                        $resourceModuleDuration = Duration::select('title')->where('id', $resource_module->duration_id)->first();
                    }

                    $resourceModuleLevel['title'] = 'None';
                    if ($resource_module->level_id) {
                        $resourceModuleLevel = Levels::select('title')->where('id', $resource_module->level_id)->first();
                    }

                    $summaryData[$countdata]['id'] = $key + 1;
                    $summaryData[$countdata]['module'] = 'resource';
                    $summaryData[$countdata]['module_id'] = $resource_module->id;
                    $summaryData[$countdata]['title'] = $resource_module->title;
                    $summaryData[$countdata]['mediaType'] = 'image';
                    $summaryData[$countdata]['cover_image'] = $coverImage;
                    $summaryData[$countdata]['deadline'] = 'none';
                    $summaryData[$countdata]['duration'] = $resourceModuleDuration['title'];
                    $summaryData[$countdata]['level'] = $resourceModuleLevel['title'];
                    $summaryData[$countdata]['updated_at'] = $resource_module->updated_at;
                    $summaryData[$countdata]['members'] = 0;
                    $summaryData[$countdata]['tsubmition'] = 0;
                    $summaryData[$countdata]['status'] = $resource_module->status == '1' ? 'Open' : 'None';
                    $summaryData[$countdata]['achievementname'] = null;
                    $summaryData[$countdata]['is_earnable'] = null;
                }
            }

            return $summaryData;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getCompletedModuleCount($type, $user, $summary_date)
    {
        try {
            $completedLab = ModuleCompletionStatus::where(['user_id' => $user->id, 'module_type' => '0', 'percentage' => '100', 'is_completed' => '1'])->count();
            $completedPoints = $user->user_points;
            $achievements = UserAchievement::where('user_id', $user->id)->count();
            $challenge_paths = UserAchievement::where(['user_id' => $user->id, 'achievement_type' => '3'])->count();
            $lab_programs = UserAchievement::where(['user_id' => $user->id, 'achievement_type' => '1'])->count();
            $resources = UserAchievement::where(['user_id' => $user->id, 'achievement_type' => '4'])->count();
            $completeChallenge = UserAchievement::where(['user_id' => $user->id, 'achievement_type' => '10'])->count();

            $challengeCompleted = Project::where(['user_id' => '178', 'is_submitted' => '1'])->pluck('challenge_id');
            $challengesArray = Challenge::whereIn('id', $challengeCompleted)->get();
            $verified_skills_array = array();
            if ($challengesArray->count() > 0) {
                foreach ($challengesArray as $challenge) {
                    if ($challenge->skills) {
                        $verified_skills_ids = $challenge->skills->pluck('foreign_id');
                        foreach ($verified_skills_ids as $sval) {
                            if ($sval != '') {
                                $verified_skills_array[] = $sval;
                            }
                        }
                    }
                }
            }

            return ['labs' => $completedLab, 'lab_programs' => $lab_programs, 'challenges' => $completeChallenge, 'challenge_paths' => $challenge_paths, 'resources' => $resources, 'achievements' => $achievements, 'learning_points' => $completedPoints, 'verified_skills' => count($verified_skills_array)];
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getTopAchievements($type, $user, $summaryType)
    {
        try {
            $achievementData = [];
            $achievements = UserAchievement::select('id', 'title', 'achievement_image');
            if ($summaryType == 'lab') {
                $achievements = $achievements->where('achievement_type', '0');
            } elseif ($summaryType == 'challenge') {
                $achievements = $achievements->where('achievement_type', '2');
            }

            $achievements = $achievements->where('user_id', $user->id)->limit(3)->orderBy('issue_date', 'desc')->get();
            if (!empty($achievements)) {
                foreach ($achievements as $key => $achievement) {
                    $achievementData[$key]['id']    = $key;
                    $achievementData[$key]['table_id']    = $achievement->id;
                    $achievementData[$key]['name']  = $achievement->title;
                    $achievementData[$key]['image'] = config('site-settings.cdn_url') . $achievement->achievement_image;
                }
            }

            if ($user->preferred_language == 'fr-CA') {
                $unlockMore = 'Débloquez plus';
            } else {
                $unlockMore = 'Unlock More';
            }

            $imageUrl = env('CDN_URL') . 'public/front/img/react-email/unlock-more.png';
            if (!empty($achievements) && count($achievements) == 2) {
                $emptyArry = [['name' => $unlockMore, 'image' => $imageUrl]];
                $achievementData = array_merge($achievementData, $emptyArry);
            } elseif (!empty($achievements) && count($achievements) == 1) {
                $emptyArry = [['name' => $unlockMore, 'image' => $imageUrl], ['name' => $unlockMore, 'image' => $imageUrl]];
                $achievementData = array_merge($achievementData, $emptyArry);
            }

            return $achievementData;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function summaryLanguageContent($language)
    {
        try {
            if ($language == 'fr-CA') {
                return  [
                    'subjectnetwork' => 'Prepr - Voici votre dernier résumé du réseau PreprLabs !',
                    'subjectlab'    => 'Prepr - Voici votre dernier résumé de laboratoire sur PreprLabs!',
                    'subjectchallenge' => 'Prepr - Voici votre dernier résumé de Challenge sur PreprLabs!',
                    'plms'          => 'Résumé mensuel de PreprLabs',
                    'plws'          => 'Résumé hebdomadaire de PreprLabs',
                    'wsntm'         => "Quoi de neuf ce mois-ci",
                    'wsntw'         => "Quoi de neuf cette semaine",
                    'deadline'      => 'Date limite',
                    'duration'      => 'Durée',
                    'level'         => 'Niveau',
                    'eap'           => 'Explorer toutes les mises à jour',
                    'uhc'           => 'Vous avez terminé',
                    'labs'          => 'Laboratoires',
                    'labspro'       => 'Programme de laboratoires',
                    'challnege'     => 'Défi',
                    'challnegepath'  => 'Chemin du défi',
                    'resource'      => 'Ressource',
                    'achievement'   => 'Réalisation',
                    'points'        => 'Points',
                    'verifiedskills' => 'Compétences vérifiées',
                    'yourtta'       => 'Vos 3 meilleures réalisations',
                    'wruinteracted' => 'Ce avec quoi vous avez le plus interagi',
                    'onpreprlab'    => 'Sur PreprLabs',
                    'emloc'         => "Explorez plus de laboratoires ou de défis et poursuivez votre parcours d'apprentissage maintenant.",
                    'explore'       => 'Explorer',
                    'umbi'          => 'Vous etes peut etre intéressé',
                    'tewst'         => 'Cet e-mail a été envoyé à',
                    'arr'           => 'Tous les droits sont réservés.',
                    'byorc'         => "car vous avez accepté de recevoir des communications de PreprLabs. Vous ne souhaitez pas recevoir ces e-mails ?",
                    'unsubscribe'   => 'Se désabonner',
                    'ulabs'         => 'Résumé de votre laboratoire',
                    'uhcted'        => 'Vous avez terminé',
                    'explorelab'    => 'Explorer les laboratoires',
                    'ttlyiw'        => 'Top 3 des laboratoires avec lesquels vous avez interagi',
                    'most'          => 'la plupart',
                    'lymbii'        => 'Laboratoires qui pourraient vous intéresser',
                    'ycs'           => 'Résumé de votre défi',
                    'ttcamt'        => 'Top 3 des réalisations du défi',
                    'ttcyi'         => 'Top 3 des défis auxquels vous avez interagi',
                    'withmost'      => 'avec la plupart',
                    'exploremccj'   => "Explorez plus de défis et poursuivez votre parcours d'apprentissage maintenant.",
                    'cymii'         => 'Défis qui pourraient vous intéresser',
                    'explorechallenge'  => 'Explorer le défi',
                    'viewalla'      => 'Voir toutes les réalisations',
                    'notdata1'      => 'Explorez plus de laboratoires ou de défis et',
                    'notdata2'      => "continuez votre voyage d'apprentissage maintenant.",
                    'members'       => "Membres",
                    'lupdate'       => 'Dernière mise à jour',
                    'tsubmition'    => 'Soumissions totales',
                    'status'        => 'Statut',
                    'exploremlc'    => 'Explorez plus de laboratoires ou de défis et continuez votre',
                    'lejn'          => "voyage d'apprentissage maintenant.",
                    'emcao'         => 'Explorez plus de composants et obtenez',
                    'abctm'         => 'réalisations en les complétant.',
                    'emlauy'        => 'Explorez plus de laboratoires et débloquez votre',
                    'proov'         => 'aperçu des progrès.',
                    'emlacydd'      => "Explorez d'autres laboratoires et poursuivez votre",
                    'emcauyds'      => 'Explorez plus de défis et débloquez votre ',
                    'emcacue'       => 'Explorez plus de défis et continuez',
                    'yljnw'         => "votre parcours d'apprentissage maintenant.",
                    'ernable'       => 'Réalisations à gagner',
                    'obtained'      => 'Réalisations obtenues'
                ];
            } else {
                return  [
                    'subjectnetwork' => 'Prepr - Here is your latest PreprLabs Network summary!',
                    'subjectlab'    => 'Prepr - Here is your latest Lab summary on PreprLabs!',
                    'subjectchallenge' => 'Prepr - Here is your latest Challenge summary on PreprLabs!',
                    'plms'          => 'PreprLabs Monthly Summary',
                    'plws'          => 'PreprLabs Weekly Summary',
                    'wsntm'         => "What's New This Month",
                    'wsntw'         => "What's New This Week",
                    'deadline'      => 'Deadline',
                    'duration'      => 'Duration',
                    'level'         => 'Level',
                    'eap'           => 'Explore All Updates',
                    'uhc'           => 'You Have Completed',
                    'labs'          => 'Labs',
                    'labspro'       => 'Labs Program',
                    'challnege'     => 'Challenge',
                    'challnegepath' => 'Challenge Path',
                    'resource'      => 'Resource',
                    'achievement'   => 'Achievement',
                    'points'        => 'Points',
                    'verifiedskills' => 'Verified Skills',
                    'yourtta'       => 'Your Top 3 Achievements',
                    'wruinteracted' => 'What you interacted with most',
                    'onpreprlab'    => 'On PreprLabs',
                    'emloc'         => 'Explore more Labs or Challenges and continue your learning journey now.',
                    'explore'       => 'Explore',
                    'umbi'          => 'You May Be Interested In',
                    'tewst'         => 'This email was sent to',
                    'arr'           => 'All rights reserved.',
                    'byorc'         => "because you've opted in to receive communications from PreprLabs. Don't want to receive these emails?",
                    'unsubscribe'   => 'Unsubscribe',
                    'ulabs'         => 'Your Lab Summary',
                    'uhcted'        => 'You Have Completed',
                    'explorelab'    => 'Explore Labs',
                    'ttlyiw'        => 'Top 3 Labs you interacted with',
                    'most'          => 'most',
                    'lymbii'        => 'Labs You May be Interested in',
                    'ycs'           => 'Your Challenge Summary',
                    'ttcamt'        => 'Top 3 Challenge Achievements',
                    'ttcyi'         => 'Top 3 Challenges you interacted',
                    'withmost'      => 'with most',
                    'exploremccj'   => 'Explore more Challenges and continue your learning journey now.',
                    'cymii'         => 'Challenges You May be Interested in',
                    'explorechallenge'   => 'Explore Challenges',
                    'viewalla'      => 'View All Achievements',
                    'notdata1'      => 'Explore more Labs or Challenges and',
                    'notdata2'      => "continue your learning journey now.",
                    'members'       => "Members",
                    'lupdate'       => 'Last Updated',
                    'tsubmition'    => 'Total submissions',
                    'status'        => 'Status',
                    'exploremlc'    => 'Explore more Labs or Challenges and continue your',
                    'lejn'          => 'learning journey now.',
                    'emcao'         => 'Explore more components and obtain',
                    'abctm'         => 'achievements by completing them.',
                    'emlauy'        => 'Explore more Labs and unlock your',
                    'proov'         => 'progress overview.',
                    'emlacydd'      => 'Explore more Labs and continue your',
                    'emcauyds'      => 'Explore more Challenges and unlock your ',
                    'emcacue'       => 'Explore more Challenges and continue',
                    'yljnw'         => 'your learning journey now.',
                    'ernable'       => 'Earnable Achievements',
                    'obtained'      => 'Obtained Achievements'
                ];
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function challengeRecommendationEmailSubscriptionLanguageContent($language)
    {
        try {
            if ($language == 'fr-CA') {
                return  [
                    'emailsubject'  => "Courrier électronique d'inscription au courrier électronique de recommandation du défi Prepr",
                    'title'         => 'Recommandation de défi',
                    'titlehead'     => 'Consultez votre recommandation Prepr Network Challenge',
                    'recommendationnotfound' => 'Recommandation de défi introuvable',
                    'writeus'       => 'N`hésitez pas à nous écrire',
                    'anyquery'      => 'pour toute demande',
                    'mightLikeRecommendedChallenges' => 'Des défis qui pourraient également vous plaire :',
                ];
            } else {
                return  [
                    'emailsubject'  => 'Prepr Challenge Recommendation email subscription mail',
                    'title'         => 'Challenge Recommendation',
                    'titlehead'     => 'Check out your Prepr Network Challenge Recommendation',
                    'recommendationnotfound' => 'Challenge Recommendation not found',
                    'writeus'       => 'Feel free to write us',
                    'anyquery'      => 'for any inquiries',
                    'mightLikeRecommendedChallenges' => 'Challenges you might also like:',
                ];
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
