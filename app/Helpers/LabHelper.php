<?php

namespace App\Helpers;

use App\Models\AchievementConditionList;
use App\Models\AdminChallenge;
use App\Models\ChallengeProject;
use App\Models\Challange;
use App\Models\ResourceSocialLink;
use App\Models\Resource;
use App\Models\ResourceGroup;
use App\Models\Group;
use App\Models\ResourceModuleVisit;
use App\Models\Project;
use App\Models\Lab;
use App\Models\User;
use App\Models\LabResources;
use App\Models\LabChallenges;
use App\Models\LabAchievement;
use App\Models\LabAchievementWin;
use App\Models\MemberManagement;
use Illuminate\Support\Facades\Auth;
use App\Traits\FCMTraitStatic;
use Exception;
use Illuminate\Support\Facades\App;

class LabHelper
{
    public static function getStatus($lab)
    {
        try {
            $status = 'In Progress';

            $activeChallenge = AdminChallenge::where(['is_completed' => '1', 'status' => 'open'])->where('associat_lab', 'like', '%"' . $lab->id . '"%')->count();
            $completedChallenge = AdminChallenge::where(['is_completed' => '1', 'status' => 'completed'])->where('associat_lab', 'like', '%"' . $lab->id . '"%')->count();
            $closedChallenge = AdminChallenge::where(['is_completed' => '1', 'status' => 'closed'])->where('associat_lab', 'like', '%"' . $lab->id . '"%')->count();

            if ($activeChallenge == 0 && $completedChallenge > 0 && $closedChallenge == 0) {
                $status = 'Completed';
            } elseif ($activeChallenge == 0 && $completedChallenge = 0 && $closedChallenge > 0) {
                $status = 'Closed';
            }

            return $challenges= [
                'activeChallenge' => ($activeChallenge == false) ? 0 : $activeChallenge,
                'closedChallenge' => ($closedChallenge == false) ? 0 : $closedChallenge,
                'completedChallenge' => ($completedChallenge == false) ? 0 : $completedChallenge,
                'status' => $status
            ];
        } catch (\Exception $ex) {
            return false;
        }
    }

    public static function getLabListProgress($lab, $user_id)
    {
        $isMemberLab = MemberManagement::where('module_type', 'lab')->where('module_id', $lab->id)->where('invitee_id', $user_id)->first();
        if (!empty($isMemberLab)) {

            // For Challenge section
            $challengesAssociated = Challange::with('getComment', 'creator', 'getCategory')->where('associat_lab', 'like', '%"' . $lab->id . '"%')->pluck('id')->toArray();
            $labChallenges = LabChallenges::where('lab_id', $isMemberLab->module_id)->whereNotNull('challenge_id')->pluck('challenge_id')->toArray();
            $challengeList = array_unique(array_merge($challengesAssociated, $labChallenges));

            $userChallengePrjt = [];
            foreach ($challengeList as $value) {
                $userChallengePrjt[] = ChallengeProject::where('challange_id', $value)->where('user_id', $user_id)->count();
            }

            // For Challenge paths section
            $selectedPathList = LabChallenges::where('lab_id', $lab->id)->whereNotNull('challenge_path_id')->pluck('challenge_path_id')->toArray();
            $getChallengeList = Group::whereIn('id', $selectedPathList)->pluck('challenge_id')->toArray();
            $pathList = array_unique(explode(',', implode(",", $getChallengeList)));

            $userPathList = [];
            foreach ($pathList as $value) {
                $userPathList[] = ChallengeProject::where('challange_id', $value)->where('user_id', $user_id)->count();
            }

            // For Resource module section
            $getResources = LabResources::where('lab_id', $lab->id)->whereNotNull('resources_id')->pluck('resources_id')->toArray();
            $resourceList = Resource::whereIn('id', $getResources)->pluck('id')->toArray();
            $moduleCount = self::getmoduleCount($resourceList, $user_id);

            // For Resource collection section
            $selectedCollectionList = LabResources::where('lab_id', $lab->id)->whereNotNull('collection_id')->pluck('collection_id')->toArray();
            $collectionList = ResourceGroup::whereIn('id', $selectedCollectionList)->pluck('resource_id')->toArray();

            $collectionCount = [];
            foreach ($collectionList as $resourceList) {
                $collectionModules = self::getmoduleCount(json_decode($resourceList), $user_id);
                $collectionCount[] = $collectionModules;
            }
            $collectionModuleCount = 0;
            $collectionModuleCountVisited = 0;
            foreach ($collectionCount as $collection) {
                $collectionModuleCount         += array_sum($collection['totalAttachModule']);
                $collectionModuleCountVisited  += array_sum($collection['totalAttachModuleVisited']);
            }

            // For Resource group section
            $selectedGroupList = LabResources::where('lab_id', $lab->id)->whereNotNull('group_id')->pluck('group_id')->toArray();
            $getResourceList = Group::select('id', 'resource_id', 'collection_id')->whereIn('id', $selectedGroupList)->get()->toArray();
            $groupCount = [];
            foreach ($getResourceList as $resourceList) {
                $resourcesID    = explode(',', $resourceList['resource_id']);
                $collectionIds  = array_filter(explode(',', $resourceList['collection_id']));
                $collectionsID = [];
                if (!empty($collectionIds)) {
                    $collectionResourceList = ResourceGroup::whereIn('id', $collectionIds)->pluck('resource_id')->toArray();
                    foreach ($collectionResourceList as $collectionList) {
                        if (!empty($collectionList)) {
                            $collectionsID += json_decode($collectionList);
                        }
                    }
                } else {
                    $collectionResourceList = [];
                }
                $resourceList = array_unique(array_merge($resourcesID, $collectionsID));
                $groupModules = self::getmoduleCount($resourceList, $user_id);
                $groupCount[] = $groupModules;
            }

            $groupModuleCount = 0;
            $groupModuleCountVisited = 0;
            foreach ($groupCount as $group) {
                $groupModuleCount         += array_sum($group['totalAttachModule']);
                $groupModuleCountVisited  += array_sum($group['totalAttachModuleVisited']);
            }

            // Total count of all lab achievement to be completed
            $allDataCount = count($challengeList) + count(array_filter($pathList)) + array_sum($moduleCount['totalAttachModule']) + $collectionModuleCount + $groupModuleCount;

            // Total count of all lab achievement has been completed
            $doneDataCount = count(array_filter($userChallengePrjt)) + count(array_filter($userPathList)) + array_sum($moduleCount['totalAttachModuleVisited']) + $collectionModuleCountVisited + $groupModuleCountVisited;

            if ($allDataCount > 0) {
                $labPercent = round((($doneDataCount)/($allDataCount) * 100), 2);
            } else {
                $labPercent = 0;
            }

            return $labPercent;
        }
    }


    public static function getLabDetailedProgress($lab, $user_id)
    {
        try {
            $isMemberLab = MemberManagement::where('module_type', 'lab')->where('module_id', $lab->id)->where('invitee_id', $user_id)->first();
            $achievement_notification_data = [];
            if (!empty($isMemberLab)) {
                // For Challenge section
                $challengesAssociated = Challange::with('getComment', 'creator', 'getCategory')->where('associat_lab', 'like', '%"' . $lab->id . '"%')->pluck('id')->toArray();
                $labChallenges = LabChallenges::where('lab_id', $isMemberLab->module_id)->whereNotNull('challenge_id')->pluck('challenge_id')->toArray();
                $challengeList = array_unique(array_merge($challengesAssociated, $labChallenges));

                $userChallengePrjt = [];
                foreach ($challengeList as $value) {
                    $userChallengePrjt[] = ChallengeProject::where('challange_id', $value)->where('user_id', $user_id)->count();
                }

                // For Challenge paths section
                $selectedPathList = LabChallenges::where('lab_id', $lab->id)->whereNotNull('challenge_path_id')->pluck('challenge_path_id')->toArray();
                $getChallengeList = Group::whereIn('id', $selectedPathList)->pluck('challenge_id')->toArray();
                $pathList = array_unique(explode(',', implode(",", $getChallengeList)));

                $userPathList = [];
                foreach ($pathList as $value) {
                    $userPathList[] = ChallengeProject::where('challange_id', $value)->where('user_id', $user_id)->count();
                }

                // For Resource module section
                $getResources = LabResources::where('lab_id', $lab->id)->whereNotNull('resources_id')->pluck('resources_id')->toArray();
                $resourceList = Resource::whereIn('id', $getResources)->pluck('id')->toArray();
                $moduleCount = self::getmoduleCount($resourceList, $user_id);

                // For Resource collection section
                $selectedCollectionList = LabResources::where('lab_id', $lab->id)->whereNotNull('collection_id')->pluck('collection_id')->toArray();
                $collectionList = ResourceGroup::whereIn('id', $selectedCollectionList)->pluck('resource_id')->toArray();

                $collectionCount = [];
                foreach ($collectionList as $resourceList) {
                    $collectionModules = self::getmoduleCount(json_decode($resourceList), $user_id);
                    $collectionCount[] = $collectionModules;
                }
                $collectionModuleCount = 0;
                $collectionModuleCountVisited = 0;
                foreach ($collectionCount as $collection) {
                    $collectionModuleCount         += array_sum($collection['totalAttachModule']);
                    $collectionModuleCountVisited  += array_sum($collection['totalAttachModuleVisited']);
                }

                // For Resource group section
                $selectedGroupList = LabResources::where('lab_id', $lab->id)->whereNotNull('group_id')->pluck('group_id')->toArray();
                $getResourceList = Group::select('id', 'resource_id', 'collection_id')->whereIn('id', $selectedGroupList)->get()->toArray();
                $groupCount = [];
                foreach ($getResourceList as $resourceList) {
                    $resourcesID    = explode(',', $resourceList['resource_id']);
                    $collectionIds  = array_filter(explode(',', $resourceList['collection_id']));
                    $collectionsID = [];
                    if (!empty($collectionIds)) {
                        $collectionResourceList = ResourceGroup::whereIn('id', $collectionIds)->pluck('resource_id')->toArray();
                        foreach ($collectionResourceList as $collectionList) {
                            if (!empty($collectionList)) {
                                $collectionsID += json_decode($collectionList);
                            }
                        }
                    } else {
                        $collectionResourceList = [];
                    }
                    $resourceList = array_unique(array_merge($resourcesID, $collectionsID));
                    $groupModules = self::getmoduleCount($resourceList, $user_id);
                    $groupCount[] = $groupModules;
                }

                $groupModuleCount = 0;
                $groupModuleCountVisited = 0;
                foreach ($groupCount as $group) {
                    $groupModuleCount         += array_sum($group['totalAttachModule']);
                    $groupModuleCountVisited  += array_sum($group['totalAttachModuleVisited']);
                }

                // Total count of all lab achievement to be completed
                $allDataCount = count($challengeList) + count(array_filter($pathList)) + array_sum($moduleCount['totalAttachModule']) + $collectionModuleCount + $groupModuleCount;

                // Total count of all lab achievement has been completed
                $doneDataCount = count(array_filter($userChallengePrjt)) + count(array_filter($userPathList)) + array_sum($moduleCount['totalAttachModuleVisited']) + $collectionModuleCountVisited + $groupModuleCountVisited;

                if ($allDataCount > 0) {
                    $labPercent = round((($doneDataCount)/($allDataCount) * 100), 2);
                } else {
                    $labPercent = 0;
                }
                if ($labPercent > 0) {
                    $achievementData = LabAchievement::where('lab_id', $lab->id)->first();
                    $achievementCriteria = LabAchievement::where('lab_id', $lab->id)->pluck('achievement_condition')->toArray();
                    if (!empty($achievementCriteria)) {
                        foreach (json_decode($achievementCriteria[0]) as $criteria) {
                            $achievement_list = AchievementConditionList::where('id', $criteria)->first();
                            // for challenge achivement give code
                            if ($achievement_list->condition_title == 'Complete All Challenges') {
                                if (count(array_filter($userChallengePrjt)) > 0) {
                                    $challengeAchievement = (count(array_filter($userChallengePrjt)) == count($challengeList));
                                    if ($challengeAchievement == true) {
                                        $getCriteriaID = AchievementConditionList::where('condition_title', $achievement_list->condition_title)->first();
                                        $getChallengeAchievement = LabAchievementWin::updateOrCreate([
                                        'lab_id'                => $achievementData->lab_id,
                                        'user_id'               => $user_id,
                                        'lab_condition'         => $getCriteriaID->id,
                                        'achievement_id'        => $achievementData->id,
                                        'lab_points'            => null,
                                        'lab_achievement_image' => $achievementData->achievement_image
                                    ]);
                                    }
                                }
                            } elseif ($achievement_list->condition_title == 'Complete All Challenge Paths') {
                                if (count(array_filter($userPathList)) > 0) {
                                    $challengeAchievement = (count(array_filter($userPathList)) == count($pathList));
                                    if ($challengeAchievement == true) {
                                        $getCriteriaID = AchievementConditionList::where('condition_title', $achievement_list->condition_title)->first();
                                        $getPathAchievement = LabAchievementWin::updateOrCreate([
                                        'lab_id'                => $achievementData->lab_id,
                                        'user_id'               => $user_id,
                                        'lab_condition'         => $getCriteriaID->id,
                                        'achievement_id'        => $achievementData->id,
                                        'lab_points'            => null,
                                        'lab_achievement_image' => $achievementData->achievement_image
                                    ]);
                                    }
                                }
                            } elseif ($achievement_list->condition_title == 'Complete All Resource Modules') {
                                if (array_sum($moduleCount['totalAttachModuleVisited']) > 0) {
                                    $challengeAchievement = (array_sum($moduleCount['totalAttachModule']) == array_sum($moduleCount['totalAttachModuleVisited']));
                                    if ($challengeAchievement == true) {
                                        $getCriteriaID = AchievementConditionList::where('condition_title', $achievement_list->condition_title)->first();
                                        $getModuleAchievement = LabAchievementWin::updateOrCreate([
                                        'lab_id'                => $achievementData->lab_id,
                                        'user_id'               => $user_id,
                                        'lab_condition'         => $getCriteriaID->id,
                                        'achievement_id'        => $achievementData->id,
                                        'lab_points'            => null,
                                        'lab_achievement_image' => $achievementData->achievement_image
                                    ]);
                                    }
                                }
                            } elseif ($achievement_list->condition_title == 'Complete All Resource Collections') {
                                if ($collectionModuleCountVisited > 0) {
                                    $challengeAchievement = ($collectionModuleCount == $collectionModuleCountVisited);
                                    if ($challengeAchievement == true) {
                                        $getCriteriaID = AchievementConditionList::where('condition_title', $achievement_list->condition_title)->first();
                                        $getCollectionAchievement = LabAchievementWin::updateOrCreate([
                                        'lab_id'                => $achievementData->lab_id,
                                        'user_id'               => $user_id,
                                        'lab_condition'         => $getCriteriaID->id,
                                        'achievement_id'        => $achievementData->id,
                                        'lab_points'            => null,
                                        'lab_achievement_image' => $achievementData->achievement_image
                                    ]);
                                    }
                                }
                            } elseif ($achievement_list->condition_title == 'Complete All Resource Groups') {
                                if ($groupModuleCountVisited > 0) {
                                    $challengeAchievement = ($groupModuleCount == $groupModuleCountVisited);
                                    if ($challengeAchievement == true) {
                                        $getCriteriaID = AchievementConditionList::where('condition_title', $achievement_list->condition_title)->first();
                                        $getGroupAchievement = LabAchievementWin::updateOrCreate([
                                        'lab_id'                => $achievementData->lab_id,
                                        'user_id'               => $user_id,
                                        'lab_condition'         => $getCriteriaID->id,
                                        'achievement_id'        => $achievementData->id,
                                        'lab_points'            => null,
                                        'lab_achievement_image' => $achievementData->achievement_image
                                    ]);
                                    }
                                }
                            } elseif ($achievement_list->condition_title == 'Complete All') {
                                if ($doneDataCount > 0) {
                                    $challengeAchievement = ($doneDataCount == $allDataCount);
                                    if ($challengeAchievement == true) {
                                        $getCriteriaID = AchievementConditionList::where('condition_title', $achievement_list->condition_title)->first();
                                        $getAllAchievement = LabAchievementWin::updateOrCreate([
                                        'lab_id'                => $achievementData->lab_id,
                                        'user_id'               => $user_id,
                                        'lab_condition'         => $getCriteriaID->id,
                                        'achievement_id'        => $achievementData->id,
                                        'lab_points'            => null,
                                        'lab_achievement_image' => $achievementData->achievement_image
                                    ]);
                                    }
                                }
                            }
                        }

                        $achievementCheckCount = $achievementData->achievement_condition;
                        $winCheck = LabAchievementWin::where(['lab_id' => $lab->id, 'user_id' => $user_id, 'lab_points' => null])->count();
                        if (count(json_decode($achievementCheckCount)) == $winCheck) {
                            if ($achievementData) {
                                $markAchievement = LabAchievementWin::where(['user_id' => $user_id ,'lab_id' => $achievementData->lab_id, 'achievement_id' => '0'])->count();
                                if ($markAchievement == 0) {
                                    $giveAchievement = LabAchievementWin::Create([
                                    'lab_id'                => $achievementData->lab_id,
                                    'user_id'               => $user_id,
                                    'lab_condition'         => 'All criteria completed and ' .$user_id. ' recieved lab achievement',
                                    'achievement_id'        => '0',
                                    'lab_points'            => $achievementData->achievement_points,
                                    'lab_achievement_image' => $achievementData->achievement_image
                                ]);
                                    $userData = User::find($user_id);
                                    if ($userData) {
                                        $newPoint = ($userData->point + $achievementData->achievement_points);
                                        $userRank = User::where('id', $userData->id)->update(['point' => $newPoint]);
                                    }

                                    // FCM Push Notification
                                    $fcm_token = User::find($user_id)->fcm_device_token;
                                    $lab_name = Lab::find($achievementData->lab_id)->title;
                                    FCMTraitStatic::sendNotification('Congratulations!!', 'You have won the lab achievement for ' . $lab_name . '!', $fcm_token, '/achievements', $achievementData->achievement_image);
                                    $achievement_notification_data = [
                                        'title' => 'Congratulations!!',
                                        'body' => 'You have won the lab achievement for ' . $lab_name . '!',
                                        'link' => '/achievements',
                                        'image' => $achievementData->achievement_image
                                    ];
                                }
                            }
                        }
                    }
                }
                $result = [
                    'labPercent' => $labPercent,
                    'achievement_notification_data' => $achievement_notification_data
                ];

                return $result;
            } else { // if the user is not a member of the lab
                $result = [
                    'labPercent' => 0,
                    'achievement_notification_data' => $achievement_notification_data
                ];

                return $result;
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public static function getConditionList($lab)
    {
        $user_id = Auth()->user()->id;

        $selectedlabConditions = LabAchievement::where('lab_id', $lab->id)->first();
        if (!empty($selectedlabConditions)) {
            $getLabConditions = AchievementConditionList::whereIn('id', json_decode($selectedlabConditions->achievement_condition))->pluck('condition_title')->toArray();

            $getCompletedLabConditions = LabAchievementWin::where('lab_id', $lab->id)->where('user_id', $user_id)->where('lab_points', null)->pluck('lab_condition')->toArray();

            // For Lab Progress Statement
            $labProgressCompletion = 'no';
            if (!empty($getCompletedLabConditions)) {
                $checkCompletionLab = (count($getLabConditions) == count($getCompletedLabConditions));
                if ($checkCompletionLab == true) {
                    $labProgressCompletion = 'yes';
                }
            }

            // For All Condition
            if (in_array("Complete All", $getLabConditions)) {
                $allCompleted = LabAchievementWin::where('lab_id', $lab->id)->where('user_id', $user_id)->where('lab_condition', '1')->count();
                $is_AllCompleted = $allCompleted > 0 ? 'yes' : 'no';
            } else {
                $is_AllCompleted = 'Not_Applicable';
            }

            // For All Challenges
            if (in_array("Complete All Challenges", $getLabConditions)) {
                $challengeCompleted = LabAchievementWin::where('lab_id', $lab->id)->where('user_id', $user_id)->where('lab_condition', '2')->count();
                $is_ChallengeCompleted = $challengeCompleted > 0 ? 'yes' : 'no';
            } else {
                $is_ChallengeCompleted = 'Not_Applicable';
            }

            // For Path Condition
            if (in_array("Complete All Challenge Paths", $getLabConditions)) {
                $pathCompleted = LabAchievementWin::where('lab_id', $lab->id)->where('user_id', $user_id)->where('lab_condition', '3')->count();
                $is_PathCompleted = $pathCompleted > 0 ? 'yes' : 'no';
            } else {
                $is_PathCompleted = 'Not_Applicable';
            }

            // For Resource Module Condition
            if (in_array("Complete All Resource Modules", $getLabConditions)) {
                $resourcesCompleted = LabAchievementWin::where('lab_id', $lab->id)->where('user_id', $user_id)->where('lab_condition', '4')->count();
                $is_ResourcesCompleted = $resourcesCompleted > 0 ? 'yes' : 'no';
            } else {
                $is_ResourcesCompleted = 'Not_Applicable';
            }

            // For Resource Collection Condition
            if (in_array("Complete All Resource Collections", $getLabConditions)) {
                $collectionsCompleted = LabAchievementWin::where('lab_id', $lab->id)->where('user_id', $user_id)->where('lab_condition', '5')->count();
                $is_CollectionsCompleted = $collectionsCompleted > 0 ? 'yes' : 'no';
            } else {
                $is_CollectionsCompleted = 'Not_Applicable';
            }

            // For Resource Group Condition
            if (in_array("Complete All Resource Groups", $getLabConditions)) {
                $groupsCompleted = LabAchievementWin::where('lab_id', $lab->id)->where('user_id', $user_id)->where('lab_condition', '6')->count();
                $is_GroupsCompleted = $groupsCompleted > 0 ? 'yes' : 'no';
            } else {
                $is_GroupsCompleted = 'Not_Applicable';
            }

            return ['is_AllCompleted'=> $is_AllCompleted, 'is_ChallengeCompleted'=> $is_ChallengeCompleted, 'is_PathCompleted'=> $is_PathCompleted, 'is_ResourceCompleted'=> $is_ResourcesCompleted, 'is_CollectionCompleted'=> $is_CollectionsCompleted, 'is_GroupCompleted'=> $is_GroupsCompleted, 'labProgressCompletion' => $labProgressCompletion];
        } else {
            return true;
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can get lab data for public view
    @Output: return labs data array
    -------------------------------------------------------------------------------------------- */
    public static function getLabDetailedDataRender($moduleType, $labId, $page)
    {
        if ($moduleType == 'challenge') {
            $challenges = [];
            $challenges = AdminChallenge::selectRaw("challanges.id as challangeID,
                                                     challanges.title,
                                                     challanges.description,
                                                     challanges.mediaType,
                                                     challanges.cover_image,
                                                     challanges.slug,
                                                     projects.id as projectID,
                                                     labs.cha_sequence as sequenceType")
                            ->whereNotNull('lab_challenges.challenge_id')
                            ->where('lab_challenges.lab_id', (int) $labId)
                            ->where('challanges.is_completed', '1')
                            ->leftJoin('projects', function ($join) {
                                $join->on('projects.challenge_id', '=', 'challanges.id')
                                      ->where('projects.user_id', '=', Auth::user()->id);
                            })
                            ->join('lab_challenges', 'lab_challenges.challenge_id', '=', 'challanges.id')
                            ->leftJoin('labs', function ($join) use ($labId) {
                                $join->on('labs.id', '=', 'lab_challenges.lab_id')
                                      ->where('labs.id', '=', (int) $labId);
                            })
                            ->orderBy('lab_challenges.sequence_no', 'ASC')->paginate(10);
            return $challenges;
        } elseif ($moduleType == 'challengepath') {
            $challengePaths = [];
            $challengePaths = Group::selectRaw("groups.id,
                                                groups.group_image,
                                                groups.title,
                                                groups.description,
                                                labs.cha_sequence as sequenceType")
                            ->whereNotNull('lab_challenges.challenge_path_id')
                            ->where('lab_challenges.lab_id', (int) $labId)
                            ->Join('lab_challenges', 'lab_challenges.challenge_path_id', '=', 'groups.id')
                            ->leftJoin('labs', function ($join) use ($labId) {
                                $join->on('labs.id', '=', 'lab_challenges.lab_id')
                                      ->where('labs.id', '=', (int) $labId);
                            })
                            ->orderBy('lab_challenges.sequence_no', 'ASC')->paginate(10);
            return $challengePaths;
        } elseif ($moduleType == 'resourcemodule') {
            $resourceModules    = [];
            $resourceImg        = [];
            $resourceModules = Resource::selectRaw("resources.id,
                                                    resources.res_title,
                                                    resources.res_guid,
                                                    resources.res_type,
                                                    resources.res_desc,
                                                    resources.res_title_slug,
                                                    labs.res_sequence as sequenceType")
                                    ->whereNotNull('lab_resources.resources_id')
                                    ->where('lab_resources.lab_id', (int) $labId)
                                    ->Join('lab_resources', 'lab_resources.resources_id', '=', 'resources.id')

                                    ->leftJoin('labs', function ($join) use ($labId) {
                                        $join->on('labs.id', '=', 'lab_resources.lab_id')
                                              ->where('labs.id', '=', (int) $labId);
                                    })

                                    ->orderBy('lab_resources.sequence_no')->paginate(10);
            if (!empty($resourceModules)) {
                foreach ($resourceModules as $k => $v) {
                    $getImageInfo = Resource::where(['res_parent' => $v->id, 'res_type' => 'header'])->first();
                    $resourceImg[$v->id] = isset($getImageInfo->res_guid) ? $getImageInfo->res_guid : null;
                }
            }
            return ['resourceModules' => $resourceModules , 'resourceImg' => $resourceImg];
        } elseif ($moduleType == 'resourcecollection') {
            $resourceCollections = [];
            $resourceCollections = ResourceGroup::selectRaw("resourcegroup.id,
                                                          resourcegroup.title,
                                                          resourcegroup.description,
                                                          resourcegroup.image,
                                                          resourcegroup.slug,
                                                          resourcegroup.resource_id,
                                                          labs.res_sequence as sequenceType")
                                            ->whereNotNull('lab_resources.collection_id')
                                            ->where('lab_resources.lab_id', (int) $labId)
                                            ->Join('lab_resources', 'lab_resources.collection_id', '=', 'resourcegroup.id')
                                            ->leftJoin('labs', function ($join) use ($labId) {
                                                $join->on('labs.id', '=', 'lab_resources.lab_id')
                                                      ->where('labs.id', '=', (int) $labId);
                                            })
                                            ->orderBy('lab_resources.sequence_no')->paginate(10);
            return $resourceCollections;
        } elseif ($moduleType == 'resourcegroup') {
            $resourceGroups = [];
            $resourceGroups = Group::selectRaw("groups.id,
                                                groups.group_image,
                                                groups.title,
                                                groups.description,
                                                groups.resource_id,
                                                labs.res_sequence as sequenceType")
                                ->whereNotNull('lab_resources.group_id')
                                ->where('lab_resources.lab_id', (int) $labId)
                                ->Join('lab_resources', 'lab_resources.group_id', '=', 'groups.id')
                                ->leftJoin('labs', function ($join) use ($labId) {
                                    $join->on('labs.id', '=', 'lab_resources.lab_id')
                                          ->where('labs.id', '=', (int) $labId);
                                })
                                ->orderBy('lab_resources.sequence_no')->paginate(10);
            return $resourceGroups;
        }
    }

    public static function getActiveChallenges($challengeId)
    {
        $challengeData = ChallengeProject::where('challange_id', $challengeId)->where('user_id', Auth()->user()->id)->first();
        if (!empty($challengeData)) {
            return 'yes';
        } else {
            return 'no';
        }
    }

    public static function getActiveChallengePath($challengeId)
    {
        $paths = explode(",", $challengeId);
        $userProject = [];
        foreach ($paths as $value) {
            $challengeData = ChallengeProject::where('challange_id', $value)->where('user_id', Auth()->user()->id)->first();
            if (!empty($challengeData)) {
                $userProject[] = $challengeData->id;
            }
        }
        if (!empty($userProject)) {
            if (count($userProject) == count($challengeData)) {
                return 'yes';
            } else {
                return 'no';
            }
        } else {
            return 'no';
        }
    }

    public static function getModules($resourceId, $moduleSet)
    {
        if ($moduleSet == 'module') {
            $moduleStatus = LabHelper::getActiveModule($resourceId);
            if ($moduleStatus == 'yes') {
                return 'yes';
            } else {
                return 'no';
            }
        } elseif ($moduleSet == 'collection') {
            $moduleActive = LabHelper::getActiveModule($resourceId);
            return $moduleActive;
        } elseif ($moduleSet == 'group') {
            $moduleActive = LabHelper::getActiveModule($resourceId);
            return $moduleActive;
        }
    }

    public static function getActiveModule($resource)
    {
        // users need to be visited
        $resourceWebLinkCount = ResourceSocialLink::where('resource_id', $resource)->count();
        $resourceDocumentCount = Resource::where('res_parent', $resource)->where('res_type', 'document')->count();
        $resourceAudioCount = Resource::where('res_parent', $resource)->where('res_type', 'audio')->count();
        $resourceVideoCount = Resource::where('res_parent', $resource)->where('res_type', 'video')->count();
        $resourceEmbeddedVideoCount = Resource::where('res_parent', $resource)->where('res_type', 'embedded')->count();

        // users visited code
        $userVisitedSocialLinks = ResourceModuleVisit::where(['user_id' => Auth()->user()->id, 'res_parent_id' => $resource])->where('filetype', 'web')->count();
        $userVisitedDocument = ResourceModuleVisit::where(['user_id' => Auth()->user()->id, 'res_parent_id' => $resource])->where('filetype', 'doc')->count();
        $userVisitedAudio = ResourceModuleVisit::where(['user_id' => Auth()->user()->id, 'res_parent_id' => $resource])->where('filetype', 'audio')->count();
        $userVisitedVideo = ResourceModuleVisit::where(['user_id' => Auth()->user()->id, 'res_parent_id' => $resource])->where('filetype', 'video')->count();
        $userEmbeddedVisitedVideo = ResourceModuleVisit::where(['user_id' => Auth()->user()->id, 'res_parent_id' => $resource])->where('filetype', 'embedded')->count();

        if ($userVisitedSocialLinks >= $resourceWebLinkCount && $userVisitedDocument >= $resourceDocumentCount && $userVisitedAudio >= $resourceAudioCount && $userVisitedVideo >= $resourceVideoCount && $userEmbeddedVisitedVideo >= $resourceEmbeddedVideoCount) {
            return 'yes';
        }
    }

    public static function getmoduleCount($resourceList, $user_id)
    {
        $totalAttachModule = [];
        $totalAttachModuleVisited = [];
        foreach ($resourceList as $key => $resource) {
            // get resource social link count
            $resourceWebLinkCount = ResourceSocialLink::where('resource_id', $resource)->count();
            // get resource audio count
            $resourceAudioCount = Resource::where('res_parent', $resource)->where('res_type', 'audio')->count();
            // get resource video count
            $resourceVideoCount = Resource::where('res_parent', $resource)->where('res_type', 'video')->count();
            // get resource embedded video count
            $resourceEmbeddedVideoCount = Resource::where('res_parent', $resource)->where('res_type', 'embedded')->count();
            // get resource document count
            $resourceDocumentCount = Resource::where('res_parent', $resource)->where('res_type', 'document')->count();
            // for total module fetch
            $totalAttachModule[] = ($resourceWebLinkCount + $resourceAudioCount + $resourceVideoCount + $resourceDocumentCount + $resourceEmbeddedVideoCount);

            // get user watch social links
            $userVisitedSocialLinks = ResourceModuleVisit::where(['user_id' => $user_id, 'res_parent_id' => $resource])->where('filetype', 'web')->count();
            // get user watch documents
            $userVisitedDocument = ResourceModuleVisit::where(['user_id' => $user_id, 'res_parent_id' => $resource])->where('filetype', 'doc')->count();
            // get user watch audio
            $userVisitedAudio = ResourceModuleVisit::where(['user_id' => $user_id, 'res_parent_id' => $resource])->where('filetype', 'audio')->count();
            // get user watch video
            $userVisitedVideo = ResourceModuleVisit::where(['user_id' => $user_id, 'res_parent_id' => $resource])->where('filetype', 'video')->count();
            // get user watch embedded video
            $userVisitedEmbeddedVideo = ResourceModuleVisit::where(['user_id' => $user_id, 'res_parent_id' => $resource])->where('filetype', 'embedded')->count();
            // for total module visited
            $totalAttachModuleVisited[] = ($userVisitedSocialLinks + $userVisitedDocument + $userVisitedAudio + $userVisitedVideo + $userVisitedEmbeddedVideo);
        }
        return ['totalAttachModule' => $totalAttachModule , 'totalAttachModuleVisited'=> $totalAttachModuleVisited];
    }

    public static function getLabAchievementCondition()
    {
        try {
            $todo_achievement_list = AchievementConditionList::pluck('condition_title', 'id');
            $elementTrans = [];
            if (!empty($todo_achievement_list)){
                foreach ($todo_achievement_list as $element) {
                    switch ($element) {
                        case 'Complete All':
                            $elementTrans[] = __('labels.labels_lab_conditionsca');
                        break;
                        case 'Complete All Challenges':
                            $elementTrans[] = __('labels.labels_lab_conditionscc');
                        break;
                        case 'Complete All Challenge Paths':
                            $elementTrans[] = __('labels.labels_lab_conditionscp');
                        break;
                        case 'Complete All Resource Modules':
                            $elementTrans[] = __('labels.labels_lab_conditionscrm');
                        break;
                        case 'Complete All Resource Collections':
                            $elementTrans[] = __('labels.labels_lab_conditionscrc');
                        break;
                        case 'Complete All Resource Groups':
                            $elementTrans[] = __('labels.labels_lab_conditionscarg');
                        break;
                    }
                }
            }
            return $elementTrans;
        } catch (\Exception $ex) {
            return [];
        }
    }
}
