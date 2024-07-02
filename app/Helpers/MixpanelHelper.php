<?php

namespace App\Helpers;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\ComponentAssociation;
use App\Models\Lab;
use App\Models\MemberManagement;
use App\Models\Organization;
use App\Models\OrganizationInviteUser;
use App\Models\Project;
use App\Models\ProjectIndustry;
use App\Models\ProjectType;
use App\Models\Tag;
use App\Models\User;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use App\Services\SkillService;
use App\Services\TagService;
use Exception;
use Mixpanel;

class MixpanelHelper
{
    // Mixpanel function used my phpunit to test connection
    public static function connection()
    {
        try {
            $mp = Mixpanel::getInstance(env('MIXPANEL_KEY'));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function mixpanel_tracking($event, $data, $user = null, $ip = 0, $lab_groups = [], $user_id = null)
    {
        try {
            $organization = null;
            $user_type = null;
            $user_type = null;
            $profile_section = null;
            $profile_section_data = null;
            $quantity = 1;
            $data_array = [];
            switch ($event) {
                // Mixpanel data: user sign up
                case config('mixpanel.sign_up'):
                    $user_type = $data['user_type'];
                    $data_array = [
                        'sign_up_type' => 'network',
                        'user_status'  => $data['status'],
                        'user_type'    => $data['user_type'],
                    ];
                    break;
                case config('mixpanel.org_sign_up'): // Mixpanel data: organization manager sign up
                    $user_type = $data['user_type'];
                    $data_array = [
                        'sign_up_type'      => 'network',
                        'organization_name' => $data['organization_name'],
                        'organization_type' => $data['user_type'],
                    ];
                    break;
                case config('mixpanel.login_success'): // Mixpanel data: login success
                case config('mixpanel.login_fail'): // Mixpanel data: login fail
                    $data_array = [
                        'status' => $data,
                    ];
                    break;
                case config('mixpanel.update_profile'): // Mixpanel data: update profile
                    $profile_section = $data['type'];
                    $profile_section_data = json_encode($data['info']);
                    $data_array = [
                        'profile_section' => $profile_section,
                        'updated_info'    => $profile_section_data,
                    ];
                    break;
                case config('mixpanel.complete_profile'): // Mixpanel data: complete profile
                    $data_array = [
                        'last_update' => $data,
                    ];
                    break;
                case config('mixpanel.create_lab'): // Mixpanel data: create lab
                case config('mixpanel.edit_lab'): // Mixpanel data: edit lab
                case config('mixpanel.delete_lab'): // Mixpanel data: delete lab
                    $final_lab_skills = [];
                    foreach ($data->skills as $lab_skill) {
                        $final_lab_skills[] = SkillService::getSkillBasedOnId($lab_skill)->title;
                    }
                    $final_lab_tags = [];
                    $new_tags = $data->tags;
                    foreach ($new_tags as $lab_tag) {
                        $tag = Tag::find(intval($lab_tag));
                        if ($tag) {
                            $final_lab_tags[] = $tag->tag;
                        }
                    }
                    $organization = OrganizationService::getOrganizationExistBasedOnUuid($data->organization_id)->id;
                    $data_array = [
                        'lab_title'    => $data->title,
                        'lab_tags'     => $final_lab_tags,
                        'lab_skills'   => $final_lab_skills,
                        'lab_groups'   => $lab_groups,
                        'lab_category' => Category::find($data->category_id)->title,
                        'lab_privacy'  => $data->privacy,
                    ];
                    break;
                case config('mixpanel.join_lab'): // Mixpanel data: join lab
                    $organization = $data->organization_id;
                    $data_array = [
                        'lab_privacy'  => $data->privacy,
                        'lab_title'    => $data->title,
                        'lab_category' => Category::find($data->category)->name,
                    ];
                    break;
                case config('mixpanel.view_lab'):
                    $organization = $data->organization_id;
                    $data_array = [
                        'title'  => $data->title,
                        'status' => $data->status,
                    ];
                    break;
                case config('mixpanel.leave_lab'): // Mixpanel data: leave lab
                    $organization = $data->organisation_id;
                    $quantity = -1;
                    $data_array = [
                        'lab_privacy'  => $data->privacy,
                        'lab_title'    => $data->title,
                        'lab_category' => Category::find($data->category)->name,
                    ];
                    break;
                case config('mixpanel.complete_challenge_path'): // Mixpanel data: complete challenge path
                    $organization = $data->organisation;
                    $path_challenges = [];
                    $all_path_challenges = explode(',', $data->challenge_id);
                    foreach ($all_path_challenges as $path_challenge) {
                        $path_challenges[] = Challenge::find($path_challenge)->title;
                    }

                    $data_array = [
                        'title'      => $data->title,
                        'challenges' => $path_challenges,
                        'points'     => $data->points,
                    ];
                    break;
                case config('mixpanel.create_challenge'): // Mixpanel data: create challenge
                case config('mixpanel.edit_challenge'): // Mixpanel data: edit challenge
                case config('mixpanel.delete_challenge'): // Mixpanel data: delete challenge
                    $final_challenge_skills = [];
                    $final_challenge_tags = [];
                    if (isset($data->skills)) {
                        $final_challenge_skills = SkillService::getSkillBasedOnIds($data->skills)->pluck('title');
                    }
                    if (isset($data->tags)) {
                        $final_challenge_tags = TagService::getTagsBasedOnIds($data->tags)->pluck('title');
                    }
                    $final_associate_labs = [];
                    $associated_lab_ids = ComponentAssociation::where('challenge_id', $data->id)->whereNotNull('lab_id')->pluck('lab_id');
                    foreach ($associated_lab_ids as $associate_lab) {
                        $final_associate_labs[] = LabService::getLabBasedOnId($associate_lab);
                    }

                    $organization = $data->organisation_id;
                    $data_array = [
                        'challenge_title'                 => $data->title,
                        'challenge_category'              => Category::find($data->category_id)->title,
                        'associated_lab'                  => $final_associate_labs,
                        'challenge_skills'                => $final_challenge_skills,
                        'challenge_tags'                  => $final_challenge_tags,
                        'challenge_status'                => $data->status,
                        'challenge_privacy'               => $data->privacy,
                        'minimum_rank'                    => $data->min_ranks,
                        'minimum_points'                  => $data->min_points,
                        'project_submission_requirements' => $data->projectSubmissionRequirements,
                        'dates_type'                      => $data->dates,
                    ];
                    break;
                case config('mixpanel.create_resource'): // Mixpanel data: create resource
                case config('mixpanel.edit_resource'): // Mixpanel data: edit resource
                case config('mixpanel.delete_resource'): // Mixpanel data: delete resource
                    $organization = $data->organization_id;
                    $data_array = [
                        'resource_title'  => $data->title,
                        'resource_status' => $data->status,
                        'resource_skills' => $data->skills,
                    ];
                    break;
                case config('mixpanel.view_resource'): // Mixpanel data: view resource
                    $organization = $data->org_id;
                    $data_array = [
                        'resource_title'  => $data->title,
                        'resource_status' => $data->status,
                        //   'resource_skills' => $data->skills
                    ];
                    break;
                case config('mixpanel.view_resource_collection'): // Mixpanel data: view resource collection
                    $organization = $data->org_id;
                    $data_array = [
                        'title'  => $data->title,
                        'status' => $data->status,
                    ];
                    break;
                case config('mixpanel.view_resource_group'): // Mixpanel data: view resource group
                    $organization = $data->organisation;
                    $data_array = [
                        'title'  => $data->title,
                        'status' => $data->status,
                    ];
                    break;

                case config('mixpanel.create_org'): // Mixpanel data: create organization
                    $data_array = [
                        'org_name'     => $data->title,
                        'org_category' => Category::where('id', $data->category)->first()->title,
                    ];
                    break;
                case config('mixpanel.view_org'): // Mixpanel data: view organization
                    $data_array = [
                        'org_name'     => $data->title,
                        'org_category' => Category::where('id', $data->category)->first()->title,
                    ];
                    break;
                case config('mixpanel.send_trophy'): // Mixpanel data: send trophy (via maestro)
                    $users_list = [];
                    foreach ($data as $single_data) {
                        $users_list[] = $single_data['to_name'];
                    }
                    $data_array = [
                        'trophy_name'      => $data[0]['trophyName'],
                        'trophy_code'      => $data[0]['trophyCodeID'],
                        'trophy_receivers' => $users_list,
                    ];
                    break;
                case config('mixpanel.update_sent_trophy'): // Mixpanel data: update sent trophy (via maestro)
                    $users_list = [];
                    foreach ($data as $single_data) {
                        $users_list[] = $single_data['to_name'];
                    }
                    $data_array = [
                        'trophy_name'      => $data[0]['trophyName'],
                        'trophy_code'      => $data[0]['trophyCodeID'],
                        'trophy_receivers' => $users_list,
                    ];
                    break;
                case config('mixpanel.redeem_trophy'): // Mixpanel data: redeem trophy
                    $user = User::find($user_id);
                    $data_array = [
                        'trophy_name'   => $data['name'],
                        'trophy_code'   => $data['trophy_code_id'],
                        'points_gained' => $data['points_gained'],
                    ];
                    break;
                case config('mixpanel.send_invite'): // Mixpanel data: update sent invite
                    $memberData = MemberManagement::where('id', $data)->first();
                    $user = User::where('id', $memberData->inviter_id)->first();
                    $module_name = null;
                    if ($memberData->module_type == '1') {
                        $type = 'lab';
                        $organization = Lab::where('id', $memberData->module_id)->first()->organisation;
                        $module_name = Lab::where('id', $memberData->module_id)->first()->title;
                    } elseif ($memberData->module_type == '2') {
                        $type = 'challenge';
                        $organization = Challenge::where('id', $memberData->module_id)->first()->organisation;
                        $module_name = Challenge::where('id', $memberData->module_id)->first()->title;
                    } elseif ($memberData->module_type == '3') {
                        $type = 'project';
                        $challenge_id = Project::where('id', $memberData->module_id)->first()->challenge_id;
                        $module_name = Project::where('id', $memberData->module_id)->first()->title;
                        $organization = Challenge::where('id', $challenge_id)->first()->organisation;
                    }
                    switch ($memberData->invite_type) {
                        case '0':
                            $inviteType = 'email';
                            break;
                        case '1':
                            $inviteType = 'network';
                            break;
                        case '2':
                            $inviteType = 'job_request';
                            break;
                        case '3':
                            $inviteType = 'csv';
                            break;
                    }
                    $getUserId = User::where('email', $memberData->email)->pluck('id');
                    if (isset($getUserId->id) && !empty($getUserId->id)) {
                        $inviteId = $getUserId->id;
                    } else {
                        $inviteId = $memberData->invitee_id;
                    }
                    $data_array = [
                        'invite_type'   => $inviteType,
                        'invitee_id'    => $inviteId,
                        'invitee_email' => $memberData->email,
                        'module_type'   => $type,
                        'module_name'   => $module_name,
                    ];
                    break;
                case config('mixpanel.send_org_member_invite'):
                    $memberData = OrganizationInviteUser::where('id', $data)->first();
                    $user = User::where('id', $memberData->inviter_id)->first();
                    $organization = $memberData->organisation_id;
                    $data_array = [
                        'invite_type'   => $memberData->invite_type,
                        'invitee_id'    => $memberData->user_id,
                        'invitee_email' => $memberData->email,
                        'role'          => $memberData->role,
                    ];
                    break;
                case config('mixpanel.create_project'): // Mixpanel data: create project
                case config('mixpanel.submit_project'): // Mixpanel data: submit project
                    $project_associated_lab = [];
                    $project_associated_challenge = null;
                    $project_associated_type = null;
                    $project_associated_category = null;
                    $project_associated_industry = null;
                    if (!empty($data->associate_lab)) {
                        foreach ($data->associate_lab as $lab) {
                            if (Lab::where('id', $lab)->count() > 0) {
                                $project_associated_lab[] = Lab::where('id', $lab)->first()->title;
                            }
                        }
                    }
                    if ($data->challenge_id != null) {
                        if (Challenge::where('uuid', $data->challenge_id)->count() > 0) {
                            $project_associated_challenge = Challenge::where('uuid', $data->challenge_id)->first()->title;
                        }
                    }
                    if ($data->type != null) {
                        if (ProjectType::where('id', $data->type)->count() > 0) {
                            $project_associated_type = ProjectType::where('id', $data->type)->first()->name;
                        }
                    }
                    if ($data->category != null) {
                        if (Category::where('id', $data->category)->count() > 0) {
                            $project_associated_category = Category::where('id', $data->category)->first()->name;
                        }
                    }
                    if ($data->industry != null) {
                        if (ProjectIndustry::where('id', $data->industry)->count() > 0) {
                            $project_associated_industry = ProjectIndustry::where('id', $data->industry)->first()->name;
                        }
                    }
                    if ($project_associated_challenge != null) {
                        $organization = Challenge::where('uuid', $data->challenge_id)->first()->organisation;
                    }
                    $data_array = [
                        'project_title'        => $data->title,
                        'associated_lab'       => $project_associated_lab,
                        'associated_challenge' => $project_associated_challenge,
                        'project_type'         => $project_associated_type,
                        'project_category'     => $project_associated_category,
                        'project_industry'     => $project_associated_industry,
                    ];
                    break;
                case config('mixpanel.fav_or_unfav'): // Mixpanel data: favourite a lab/challenge
                    $data_array = [
                        'fav_or_unfav' => $data['fav_or_unfav'],
                        'type'         => $data['fav_type'],
                    ];
                    if ($data['fav_or_unfav'] == 'unfav') {
                        $quantity = -1;
                    }
                    break;
                case config('mixpanel.user_comment'):
                case config('mixpanel.vote_project'):
                    $data_array = $data;
                    break;
                case config('mixpanel.unvote_project'):
                    $data_array = $data;
                    $quantity = -1;
                    break;
                case config('mixpanel.push_notification'): // Mixpanel data: push notifications
                    $data_array = $data;
                    break;
                case config('mixpanel.earn_achievement'): // Mixpanel data: earn achievements
                    $data_array = $data;
                    $organization = $data['org'];
                    break;
                default:
                    // code...
                    break;
            }
            $mp = app(Mixpanel::class);
            $data_array['ip'] = $ip;

            if ($user != null) {
                if (MemberManagement::where('email', $user->email)->count() > 0) {
                    $user_role = MemberManagement::where('email', $user->email)->latest()->first()->role;
                } else {
                    $user_role = 'user';
                }
                $mp->identify($user->id);
                if ($organization != null) {
                    $organization_name = Organization::where('id', $organization)->first()->title;
                    $mp->register('organization_name', $organization_name);
                }
                $mp->track($event['event_name'], $data_array);
                $profile_data = [
                    '$first_name'    => $user->first_name,
                    '$last_name'     => $user->last_name,
                    '$email'         => $user->email,
                    'user_role'      => $user_role,
                    'type'           => $user_type,
                    $profile_section => $profile_section_data,
                ];

                // Set user profile properties in Mixpanel People
                $mp->people->set($user->id, $profile_data, $ip, true);
//                $mp->people->set($user->id, array(
//                    '$name' => $user->name,
//                    '$email' => $user->email,
//                    'user_role' => $user_role,
//                    'type' => $user_type,
//                    $profile_section => $profile_section_data
//                ), $ip, $ignore_time = true);

                $mp->people->increment($user->id, $event['variable_name'], $quantity);
                $mp->unregister($user->id);
            } else {
                $mp->track($event['event_name'], $data_array);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
