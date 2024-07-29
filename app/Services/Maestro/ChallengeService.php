<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\ChallengeAssessment;
use App\Models\ChallengeAssessmentCriteria;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ChallengeService
{
    public static function getChallengeCounts()
    {
        try {
            return Challenge::count();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeList()
    {
        try {
            return Challenge::where('language', LanguageService::getCurrentLanguage())->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function uploadBannerImage($request)
    {
        try {
            $achievementImage = null;
            if ($request->file('cover_image')) {
                $achievementImage = FileUploadHelper::uploadImageToS3($request->file('cover_image'), 'challenge');
            }

            return $achievementImage;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeAssociatedItemsById($challenge)
    {
        try {
            $skillIds = ChallengeSkillsGroupsStackService::getPluckSkillGroupStack($challenge);
            $labIds = ComponentAssociationService::getChallengeAssociatedLab($challenge);
            $moduleIds = ComponentAssociationService::getChallengeAssociatedResourceModule($challenge);
            $organization = !empty($challenge->organization_id) ? OrganizationService::getOrganizationById($challenge->organization_id) : null;
            $category = !empty($challenge->category_id) ? CategoryService::getCategoriesById($challenge->category_id) : null;
            $level = !empty($challenge->level_id) ? LevelsService::getLevelById($challenge->level_id) : null;
            $duration = !empty($challenge->duration_id) ? DurationService::getLevelById($challenge->duration_id) : null;
            $user = !empty($challenge->user_id) ? UserService::getUserPluckById($challenge->user_id) : null;
            $skills = !empty($challenge->id) ? SkillService::getSkillsById($skillIds) : null;
            $labs = !empty($challenge->user_id) ? LabService::getLab('edit', $labIds) : null;
            $resourceModules = !empty($challenge->user_id) ? ResourceModuleService::getResourceModulesByIds($moduleIds) : null;

            return ['category' => $category ?? [], 'organization' => $organization, 'skills' => $skills, 'skillIds' => $skillIds, 'user' => $user, 'level' => $level, 'duration' => $duration, 'labIds' => $labIds, 'labs' => $labs, 'moduleIds' => $moduleIds, 'resourceModules' => $resourceModules];
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createChallenge($request)
    {
        try {
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
            $challenge->media = self::uploadBannerImage($request);
            $challenge->status = $request->status;
            $challenge->agreement = ($request->has('agreement')) ? $request->agreement : 'No Terms and Conditions.';
            $challenge->project_privacy = $request->project_privacy;
            if ($challenge->save()) {
                return $challenge;
            }

            return false;
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

    public static function getAssessment($challengeId)
    {
        try {
            $assessment = ChallengeAssessment::where('challenge_id', $challengeId)->first();
            if ($assessment) {
                return $assessment;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCriteria($challengeId)
    {
        try {
            $criteria = ChallengeAssessmentCriteria::select('title', 'score', 'weight', 'assessment_id')->where('challenge_id', $challengeId)->get();
            if ($criteria) {
                return $criteria;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateAssessment($request)
    {
        try {
            switch ($request->assessment_type) {
                case '0':
                    $attachmentName = config('constants.assessment_type.no_evaluation');
                    $guidelines = $request->noEvGuidelines;
                    $memberEmails = null;
                    $criteria = [];
                    break;
                case '1':
                    $attachmentName = config('constants.assessment_type.open_evaluation');
                    $guidelines = $request->openEvGuidelines;
                    $memberEmails = null;
                    $guidelines = $request->openEvGuidelines;
                    $criteria = array_map(null, $request->creteria_title, $request->score, $request->weight);
                    break;
                case '2':
                    $attachmentName = config('constants.assessment_type.close_evaluation');
                    $guidelines = $request->closeEvGuidelines;
                    $memberEmails = json_encode($request->members_email);
                    $criteria = array_map(null, $request->creteria_title, $request->score, $request->weight);
                    break;
                default:
                    $attachmentName = config('constants.assessment_type.no_evaluation');
                    $guidelines = $request->noEvGuidelines;
                    $memberEmails = null;
                    $criteria = [];
            }
            $visibility = (isset($request->visibility) && $request->visibility == 'on') ? '1' : '0';

            if ($request->file($attachmentName)) {
                $filename = Str::random(25).'.'.$request->file($attachmentName)->getClientOriginalExtension();
                $image = Image::make($request->file($attachmentName))->resize(735, 415)->stream();
                $img = Storage::disk('s3')->put('uploads/challenge/assessment/'.$filename, $image);
                $attachment = 'uploads/challenge/assessment/'.$filename;
            } else {
                $attachment = null;
            }

            if ($request->request_type == 'create') {
                ChallengeAssessment::create(['challenge_id' => $request->challenge_id, 'assessment_type' => $request->assessment_type, 'visibility' => $visibility, 'members_email' => $memberEmails, 'guidelines' => $guidelines, 'attachments' => $attachment]);
            } elseif ($request->request_type == 'update') {
                ChallengeAssessment::where('id', $request->assessment_id)->update(['challenge_id' => $request->challenge_id, 'assessment_type' => $request->assessment_type, 'visibility' => $visibility, 'members_email' => $memberEmails, 'guidelines' => $guidelines, 'attachments' => $attachment]);
            }
            if (ChallengeAssessmentCriteria::where('challenge_id', (int) $request->challenge_id)->exists()) {
                ChallengeAssessmentCriteria::where('challenge_id', (int) $request->challenge_id)->delete();
            }
            if (!empty($criteria)) {
                $criteriaNewArray = [];
                foreach ($criteria as $key => $criteriaObj) {
                    $criteriaObjData['challenge_id'] = (int) $request->challenge_id;
                    $criteriaObjData['assessment_id'] = (int) $request->assessment_id;
                    $criteriaObjData['title'] = $criteriaObj[0];
                    $criteriaObjData['score'] = (int) $criteriaObj[1];
                    $criteriaObjData['weight'] = (int) $criteriaObj[2];
                    $criteriaNewArray[] = $criteriaObjData;
                }
                ChallengeAssessmentCriteria::insert($criteriaNewArray);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallenges($request)
    {
        try {
            $challenge = Challenge::select('id', 'title')->orderBy('id', 'DESC');
            if ($request->search) {
                $challenge = $challenge->where('title', 'LIKE', '%'.$request->search.'%');
            }
            $challenge = $challenge->get()->take(20)->pluck('title', 'id');
            $count = 0;
            $json_stacks = $json_result = [];
            foreach ($challenge as $key => $challenge_to_return) {
                $json_stacks[$count]['id'] = $key;
                $json_stacks[$count]['text'] = $challenge_to_return;
                $count++;
            }
            $json_result['result'] = $json_stacks;

            return response()->json($json_result);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallenge($action, $challengeId)
    {
        try {
            $challenge = Challenge::select('title', 'id');
            if ($action == 'edit') {
                $challenge = $challenge->where(['id' => $challengeId]);
            }

            return $challenge->pluck('title', 'id');
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getChallengeBasedOnId($id)
    {
        try {
            return Challenge::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeBasedOnSlug($slug)
    {
        try {
            return Challenge::where(['slug' => $slug, 'is_accessible' => '1'])->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
