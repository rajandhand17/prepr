<?php

namespace App\Services\Maestro\Challenge;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\ChallengeAchievement;
use App\Models\ChallengeRequirement;
use App\Models\ChallengeSkillsGroupsStack;
use App\Models\ChallengeTimelines;
use App\Models\ComponentAssociation;
use App\Models\Duration;
use App\Models\Lab;
use App\Models\Language;
use App\Models\Levels;
use App\Models\Organization;
use App\Models\ResourceModule;
use App\Models\Skill;
use App\Models\User;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ChallengeService
{
    public static function getChallengeList()
    {
        try {
            return Challenge::latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getLanguage()
    {
        try {
            $language = Language::where(['status' => 1])->pluck('name', 'iso');
            if ($language != null) {
                return $language;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeAssociatedItemsById($challenge)
    {
        try {
            $skillIds = ChallengeSkillsGroupsStack::where(['challenge_id' => $challenge->id, 'type' => '0'])->pluck('foreign_id');
            $labIds = ComponentAssociation::where(['challenge_id' => $challenge->id])->pluck('lab_id');
            $moduleIds = ComponentAssociation::where(['challenge_id' => $challenge->id])->pluck('resource_module_id');
            $organization = !empty($challenge->organization_id) ? Organization::where(['id' => $challenge->organization_id])->pluck('title', 'id') : null;
            $category = !empty($challenge->category_id) ? Category::where(['id' => $challenge->category_id])->pluck('title', 'id') : null;
            $level = !empty($challenge->level_id) ? Levels::where(['id' => $challenge->level_id])->pluck('title', 'id') : null;
            $duration = !empty($challenge->duration_id) ? Duration::where(['id' => $challenge->duration_id])->pluck('title', 'id') : null;
            $user = !empty($challenge->user_id) ? User::where(['id' => $challenge->user_id])->pluck('username', 'id') : null;
            $skills = !empty($challenge->id) ? Skill::whereIn('id', $skillIds)->pluck('title', 'id') : null;
            $labs = !empty($challenge->user_id) ? Lab::whereIn('id', $labIds)->pluck('title', 'id') : null;
            $resourceModules = !empty($challenge->user_id) ? ResourceModule::whereIn('id', $moduleIds)->pluck('title', 'id') : null;

            return ['category' => $category ?? [], 'organization' => $organization, 'skills' => $skills, 'skillIds' => $skillIds, 'user' => $user, 'level' => $level, 'duration' => $duration, 'labIds' => $labIds, 'labs' => $labs, 'moduleIds' => $moduleIds, 'resourceModules' => $resourceModules];
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createChallenge($request)
    {
        try {
            $coverImage = null;
            if ($request->file('cover_image')) {
                $filename = Str::random(25).'.'.$request->file('cover_image')->getClientOriginalExtension();
                $image = Image::make($request->file('cover_image'))->resize(735, 415)->stream();
                $img = Storage::disk('s3')->put('uploads/challenge/'.$filename, $image);
                $coverImage = 'uploads/challenge/'.$filename;
            }

            $model = new Challenge();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $challenge = new Challenge();
            $challenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $challenge->language = $request->language;
            $challenge->title = $request->title;
            $challenge->slug = $slug;
            $challenge->user_id = $request->user_id;
            $challenge->organization_id = $request->organization_id;
            $challenge->category_id = $request->category;
            $challenge->duration_id = $request->duration;
            $challenge->level_id = $request->level;
            $challenge->description = $request->description;
            $challenge->is_open = $request->is_open;
            $challenge->status = $request->status;
            // $challenge->privacy = $challenge_privacy;
            $challenge->media_type = 'image';
            $challenge->media = $coverImage;
            $challenge->status = $request->status;
            $challenge->agreement = ($request->has('agreement')) ? $request->agreement : 'No Terms and Conditions.';
            $challenge->project_privacy = $request->project_privacy;
            if ($challenge->save()) {
                self::challengeRequirementsSave($request, $challenge);
                self::challengeTimelinesSave($request, $challenge);
                self::challengeSkillsGroupsStacks($request, $challenge);
                self::challengeAssociations($request, $challenge);
                self::challengeIncentives($request, $challenge);

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function challengeRequirementsSave($request, $challenge)
    {
        try {
            $challengeRequirement = new ChallengeRequirement();
            $challengeRequirement->challenge_id = $challenge->id;
            $challengeRequirement->min_rank = (int) !empty($request->min_rank) ? $request->min_rank : null;
            $challengeRequirement->min_points = (int) !empty($request->min_points) ? $request->min_points : null;
            $challengeRequirement->project_submission_requirement_ids = ['2', '3']; //$request->project_submission_requirement_ids;

            if ($challengeRequirement->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function challengeTimelinesSave($request, $challenge)
    {
        try {
            if ($request->timeline_type == '1') {
                return ChallengeTimelines::create(['challenge_id' => $challenge->id, 'timeline_type' => $request->timeline_type, 'open_call_date' => $request->open_call_date, 'open_call_date_description' => $request->open_call_date_description, 'last_call_date' => $request->last_call_date, 'last_call_date_description' => $request->last_call_date_description, 'application_deadline_date' => $request->application_deadline_date, 'application_deadline_date_description' => $request->application_deadline_date_description, 'submission_deadline_date' => $request->submission_deadline_date, 'submission_deadline_date_description' => $request->submission_deadline_date_description]);
            } elseif ($request->timeline_type == '0') {
                return ChallengeTimelines::create(['challenge_id' => $challenge->id, 'timeline_type' => $request->timeline_type, 'flexible_date_number' => $request->flexible_date_number, 'flexible_date_duration' => $request->flexible_date_duration, 'flexible_expire_deadline' => $request->flexible_expire_deadline, 'automatic_alert' => $request->automatic_alert]);
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function challengeSkillsGroupsStacks($request, $challenge)
    {
        try {
            if (!empty($request->skills)) {
                $skillNewArray = [];
                foreach ($request->skills as $skill) {
                    $skillData['challenge_id'] = $challenge->id;
                    $skillData['foreign_id'] = $skill;
                    $skillData['type'] = '0';
                    $skillNewArray[] = $skillData;
                }
                ChallengeSkillsGroupsStack::insert($skillNewArray);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function challengeAssociations($request, $challenge)
    {
        try {
            if (!empty($request->associativeLab)) {
                $labNewArray = [];
                foreach ($request->associativeLab as $key => $lab) {
                    $labData['challenge_id'] = $challenge->id;
                    $labData['lab_id'] = $lab;
                    $labData['sequence'] = $key + 1;
                    $labNewArray[] = $labData;
                }
                ComponentAssociation::insert($labNewArray);
            }

            if (!empty($request->associativeResourceModule)) {
                $resourceModuleNewArray = [];
                foreach ($request->associativeResourceModule as $key => $resourceModule) {
                    $resourceModuleData['challenge_id'] = $challenge->id;
                    $resourceModuleData['resource_module_id'] = $resourceModule;
                    $resourceModuleData['sequence'] = $key + 1;
                    $resourceModuleNewArray[] = $resourceModuleData;
                }
                ComponentAssociation::insert($resourceModuleNewArray);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function challengeIncentives($request, $challenge)
    {
        try {
            if ($request->file('incentive_trophy') && count($request->file('incentive_trophy')) > 0) {
                foreach ($request->incentive_trophy as $key => $image) {
                    $filename = Str::random(10).'.'.$image->getClientOriginalExtension();
                    $images = Image::make($image)->resize(256, 256)->stream();
                    $img = Storage::disk('s3')->put('uploads/trophy/'.$filename, $images);
                    $incentive_trophy[] = 'uploads/trophy/'.$filename;
                }
            }

            for ($i = 0, $iMax = count($request->incentive_name); $i < $iMax; $i++) {
                $incentive['challenge_id'] = $challenge->id;
                $incentive['achievement_type'] = '1';
                $incentive['achievement_name'] = @$request->incentive_name[$i];
                $incentive['achievement_prize'] = @$request->incentive_prize[$i];
                $incentive['achievement_points'] = @$request->incentive_point[$i];
                if (isset($incentive_trophy[$i])) {
                    $incentive['achievement_image'] = $incentive_trophy[$i];
                } else {
                    $incentive['achievement_image'] = '';
                }
                ChallengeAchievement::create($incentive);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteChallenge($id)
    {
        try {
            $challenge = Challenge::find($id);
            if (!empty($challenge)) {
                return $challenge->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeById($id)
    {
        try {
            $challenge = Challenge::findOrFail($id);
            if ($challenge) {
                return $challenge;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateChallengeById($id, $request)
    {
        try {
            $challenge = Challenge::find($id);
            if (!empty($challenge)) {
                if ($request->file('cover_image')) {
                    $filename = Str::random(25).'.'.$request->file('cover_image')->getClientOriginalExtension();
                    $image = Image::make($request->file('cover_image'))->resize(735, 415)->stream();
                    $img = Storage::disk('s3')->put('uploads/challenge/'.$filename, $image);
                    $coverImage = 'uploads/challenge/'.$filename;
                } else {
                    $coverImage = $challenge->media;
                }
                $challenge->title = $request->title;
                $challenge->user_id = $request->user_id;
                $challenge->organization_id = $request->organization_id;
                $challenge->category_id = $request->category;
                $challenge->duration_id = $request->duration;
                $challenge->level_id = $request->level;
                $challenge->description = $request->description;
                $challenge->is_open = $request->is_open;
                $challenge->status = $request->status;
                // $challenge->privacy = $challenge_privacy;
                $challenge->media_type = 'image';
                $challenge->media = $coverImage;
                $challenge->status = $request->status;
                $challenge->agreement = ($request->has('agreement')) ? $request->agreement : 'No Terms and Conditions.';
                $challenge->project_privacy = $request->project_privacy;
                if ($challenge->save()) {
                    return true;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
