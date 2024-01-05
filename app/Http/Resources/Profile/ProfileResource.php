<?php

namespace App\Http\Resources\Profile;

use App\Services\SkillService;
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
                default:
                    $gender = 'other';
                    break;
            }
            $about = $this->userPersonal->about ? $this->userPersonal->about : null;
            $age = $this->userPersonal->age ? $this->userPersonal->age : null;
            $gender = $gender;
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
            $associatedPinned = $this->userSkills->pluck('pinned');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id')->put('pinned', $associatedPinned);
        } else {
            $skills = null;
        }

        return [
            'id'                  => $this->id,
            'first_name'          => $this->first_name,
            'last_name'           => $this->last_name,
            'full_name'           => $this->full_name,
            'username'            => $this->username,
            'email'               => $this->email,
            'country_code'        => $this->country_code,
            'phone_number'        => $this->phone_number,
            'profile_image'       => $this->profile_image,
            'pronouns'            => null,
            'project_count'       => 0,
            'lab_count'           => $this->userLabs->count(),
            'achievements'        => $this->userAchievements->count(),
            'achievements_list'   => UserAchievementResource::collection($this->userAchievements),
            'featured_achievement'=> UserAchievementResource::collection($this->userAchievements),
            'role'                => 'user',
            'challenge_history'   => [],
            'project_history'     => [],
            'friends'             => [],
            'tags'                => [],
            'about'               => $about,
            'age'                 => $age,
            'learnrank'           => '1',
            'gender'              => $gender,
            'date_of_birth'       => $dob,
            'purpose'             => $purpose,
            'user_type'           => $user_type,
            'recent_immigrant'    => $recent_immigrant,
            'indigenous_group'    => $indigenous_group,
            'visible_minority'    => $visible_minority,
            'disability'          => $disability,
            'user_experiences'    => UserExperienceResource::collection($this->userExperience),
            'user_educations'     => UserEducationResource::collection($this->userEducation),
            'user_patents'        => UserPatentResource::collection($this->userPatents),
            'user_certificates'   => UserCertificateResource::collection($this->userCertificates),
            'user_skills'         => $skills,
            'user_personal_files' => UserPersonalFilesResource::collection($this->userPersonalFiles),

        ];
    }
}
