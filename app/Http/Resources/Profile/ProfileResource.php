<?php

namespace App\Http\Resources\Profile;

use App\Services\SkillService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        switch ($this->userSetting->profile_privacy) {
            case '0':
                $profile_privacy = 'public';
                break;
            case '1':
                $profile_privacy = 'private';
                break;
            case '2':
                $profile_privacy = 'signed-user';
                break;
            default:
                $profile_privacy = 'null';
                break;
        }
        if (!auth('api')->check() && $profile_privacy == 'private' || $profile_privacy == 'private' && auth('api')->user()->id != $this->id) {
            return [
                'profile_privacy'       => $profile_privacy,
            ];
        } else {
            $purpose = null;
            $user_type = null;
            if ($this->userPersonal !== null) {
                switch ($this->userPersonal->user_type) {
                    case '0':
                        $user_type = __('responses.switch_user_type_employee');
                        break;
                    case '1':
                        $user_type = __('responses.switch_user_type_investor');
                        break;
                    case '2':
                        $user_type = __('responses.switch_user_type_teacher');
                        break;
                    case '3':
                        $user_type = __('responses.switch_user_type_job_seeker');
                        break;
                    case '4':
                        $user_type = __('responses.switch_user_type_student');
                        break;
                    case '5':
                        $user_type = __('responses.switch_user_type_recent_grad');
                        break;
                    case '6':
                        $user_type = __('responses.switch_user_type_expert');
                        break;
                    case '7':
                        $user_type = __('responses.switch_user_type_employer');
                        break;
                    case '8':
                        $user_type = __('responses.switch_user_type_recent_grad');
                        break;
                    case '9':
                        $user_type = __('responses.switch_user_type_facilitator');
                        break;
                    case '10':
                        $user_type = __('responses.switch_user_type_job_seeker');
                        break;
                    case '11':
                        $user_type = __('responses.switch_user_type_startup');
                        break;
                    case '12':
                        $user_type = __('responses.switch_user_type_learner');
                        break;
                    case '13':
                        $user_type = __('responses.switch_user_type_mentor');
                        break;
                    case '14':
                        $user_type = __('responses.switch_user_type_innovator');
                        break;
                    case '15':
                        $user_type = __('responses.switch_user_type_aspiring_entrepreneur');
                        break;
                    case '16':
                        $user_type = __('responses.switch_user_type_evaluator');
                        break;
                    case '17':
                        $user_type = __('responses.switch_user_type_small_mid_size_business');
                        break;
                    case '18':
                        $user_type = __('responses.switch_user_type_entrepreneur');
                        break;
                    case '19':
                        $user_type = __('responses.switch_user_type_ngo_charity_not_for_profit');
                        break;
                    case '20':
                        $user_type = __('responses.switch_user_type_enterprise');
                        break;
                    case '21':
                        $user_type = __('responses.switch_user_type_applicant');
                        break;
                    case '22':
                        $user_type = __('responses.switch_user_type_educational_institution');
                        break;
                    case '23':
                        $user_type = __('responses.switch_user_type_community_organization');
                        break;
                    case '24':
                        $user_type = __('responses.switch_user_type_educator');
                        break;
                    case '25':
                        $user_type = __('responses.switch_user_type_government');
                        break;
                    case '26':
                        $user_type = __('responses.switch_user_type_others');
                        break;
                    default:
                        $user_type = null;
                        break;
                }
                $gender = null;
                switch ($this->userPersonal->gender) {
                    case '0':
                        $gender = 'male';
                        break;
                    case '1':
                        $gender = 'female';
                        break;
                    case '2':
                        $gender = 'other';
                        break;
                    case '3':
                        $gender = 'decline_to_answer';
                        break;
                    default:
                        $gender = 'other';
                        break;
                }
                $about = $this->userPersonal->about ? $this->userPersonal->about : null;
                $age = $this->userPersonal->age ? $this->userPersonal->age : null;
                $gender = $this->userPersonal->gender ? $this->userPersonal->gender : null;
                $dob = $this->userPersonal->date_of_birth ? $this->userPersonal->date_of_birth : null;
                $recent_immigrant = $this->userPersonal->recent_immigrant == 1 ? 'Yes' : 'No';
                $indigenous_group = $this->userPersonal->indigenous_group == 1 ? 'Yes' : 'No';
                $visible_minority = $this->userPersonal->visible_minority == 1 ? 'Yes' : 'No';
                $disability = $this->userPersonal->disability == 1 ? 'Yes' : 'No';
                $purpose = $this->userPersonal->purpose;
            } else {
                $about = null;
                $age = null;
                $gender = null;
                $dob = null;
                $recent_immigrant = 'No';
                $indigenous_group = 'No';
                $visible_minority = 'No';
                $disability = 'No';
                $purpose = null;
            }
            if ($this->userSkills) {
                $associatedSkills = $this->userSkills->pluck('skill');
                $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
            } else {
                $skills = null;
            }
            if ($this->userTags) {
                $associatedTag = $this->userTags->pluck('tag_id');
                $userTag = TagService::getTagsBasedOnIds($associatedTag)->pluck('title', 'id');
            } else {
                $userTag = null;
            }
            if ($this->userPinnedSkills) {
                $associatedSkills = $this->userPinnedSkills->pluck('skill');
                $pinnedSkills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
            } else {
                $pinnedSkills = [];
            }
            if ($this->external_links && $this->external_links->isNotEmpty()) {
                // Map over the collection to format the data
                $formattedExternalLinks = $this->external_links->map(function ($link) {
                    return [
                        'id'        => $link->id,
                        'link_id'   => $link->social_link_id,
                        'link'      => $link->social_media_link,
                        'title'     => optional($link->social_link)->title,
                        'image'     => optional($link->social_link)->icon,
                    ];
                });
            } else {
                $formattedExternalLinks = null;
            }
            $userPersonalFiles = $this->userPersonalFiles;
            $filteredFiles = $userPersonalFiles->filter(function ($file) { return !empty(UserPersonalFilesResource::make($file)->toArray(request())); });
            $personalfiles = UserPersonalFilesResource::collection($filteredFiles);

            $isFriend = false;
            if (auth('api')->user() && auth('api')->user()->id !== $this->id) {
                $isFriend = $this->friends->where(function ($query) {
                    $query->where('user_id', auth('api')->user()->id)->orWhere('reference_id', auth('api')->user()->id);
                })->count() === 1;
            }

            $isRequestReceived = false;
            if (auth('api')->user() && auth('api')->user()->id !== $this->id) {
                $isRequestReceived = $this->friend_request_received->where('user_id', auth('api')->user()->id)->count() === 1;
            }

            $isRequestSent = false;
            if (auth('api')->user() && auth('api')->user()->id !== $this->id) {
                $isRequestSent = $this->friend_request_sent->where('reference_id', auth('api')->user()->id)->count() === 1;
            }

            return [
                'id'                      => $this->id,
                'first_name'              => $this->first_name,
                'last_name'               => $this->last_name,
                'full_name'               => $this->full_name,
                'username'                => $this->username,
                'email'                   => $this->email,
                'country_code'            => $this->country_code,
                'address'                 => isset($this->userAddress->address) ? $this->userAddress->address : null,
                'city'                    => isset($this->userAddress->city) ? $this->userAddress->city : null,
                'state'                   => isset($this->userAddress->state) ? $this->userAddress->state : null,
                'country'                 => isset($this->userAddress->country) ? $this->userAddress->country : null,
                'zip_code'                => isset($this->userAddress->zip_code) ? $this->userAddress->zip_code : null,
                'phone_number'            => $this->phone_number,
                'profile_image'           => $this->profile_image,
                'pronouns'                => null,
                'project_count'           => $this->userProjects->count(),
                'lab_count'               => $this->userLabs->count(),
                'achievements'            => $this->userAchievements->count(),
                'achievements_list'       => UserAchievementResource::collection($this->userAchievements),
                'featured_achievement'    => UserAchievementResource::collection($this->userFeaturedAchievements),
                'role'                    => 'user',
                'tags'                    => $userTag,
                'about'                   => $about,
                'age'                     => $age,
                'learnrank'               => $this->user_rank ?? 0,
                'gender'                  => $gender,
                'date_of_birth'           => $dob,
                'purpose'                 => $purpose,
                'user_type'               => $user_type,
                'recent_immigrant'        => $recent_immigrant,
                'indigenous_group'        => $indigenous_group,
                'visible_minority'        => $visible_minority,
                'disability'              => $disability,
                'is_friends'              => $isFriend ? 'Yes' : 'No',
                'is_follower'             => $this->userFollow()->exists() ? 'Yes' : 'No',
                'request_sent'            => $isRequestSent ? 'Yes' : 'No',
                'request_received'        => $isRequestReceived ? 'Yes' : 'No',
                'user_experiences'        => UserExperienceResource::collection($this->userExperience),
                'user_educations'         => UserEducationResource::collection($this->userEducation),
                'user_patents'            => UserPatentResource::collection($this->userPatents),
                'user_certificates'       => UserCertificateResource::collection($this->userCertificates),
                'user_skills'             => $skills,
                'user_pinned_skills'      => $pinnedSkills,
                'user_personal_files'     => $personalfiles,
                'friend_request_privacy'  => $this->userSetting !== null ? ($this->userSetting->friend_request_privacy == '1' ? 'yes' : 'no') : 'no',
                'profile_privacy'         => $profile_privacy,
                'external_links'          => $formattedExternalLinks,
            ];
        }
    }
}
