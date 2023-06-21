<?php

namespace App\Models;

use App\Helpers\FileUploadHelper;
use App\Helpers\LabHelper;
use App\Helpers\MemberManagementHelper;
use App\Helpers\MixpanelHelper;
use App\Helpers\PlanSubscriptionHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class Lab extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'labs';

    protected $fillable = [
        'language',
        'slug',
        'user_id',
        'organisation',
        'title',
        'verification',
        'description',
        'category',
        'privacy',
        'mediaType',
        'image',
        'member',
        'member_type',
        'latitute',
        'longitude',
        'address',
        'city',
        'country',
        'challnges',
        'lab_skills',
        'tag',
        'status',
        'phone',
        'company',
        'email',
        'website',
        'facebook',
        'linked',
        'total_share',
        'twitter',

    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function labUsers()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function list($request)
    {
        try {
            $lab = static::select('title', 'slug', 'description', 'image', 'member', 'address', 'city', 'country');

            $lab = $lab->take(20)->get();
            if (!$lab->isEmpty()) {
                return $lab;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deletes($request)
    {
        try {
            $is_exists = static::where('slug', $request->slug)->first();
            if (!$is_exists) {
                $response = ['success' => false, 'message' => __('notification.notification_lab_nf')];

                return $response;
            }
            $labs = static::where('slug', $request->slug)->delete();
            if ($labs) {
                $response = ['success' => true, 'message' => __('notification.notification_lds')];

                return $response;
            } else {
                return false;
            }
        } catch(\Exception $e) {
            return false;
        }
    }

    public function view($request)
    {
        try {
            $lab = static::select('title', 'slug', 'description', 'image', 'member', 'address', 'city', 'country')->where('slug', $request->slug)->first();
            if (!$lab->isEmpty()) {
                return $lab;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            $lab_count = Lab::where('user_id', Auth::id())->where('is_auto_created', '0')->count();
            if (!empty($request->organisation)) {
                $organisations_lab_limit = Lab::where('organisation', $request->organisation)->where('is_auto_created', '0')->count();
                $labs_limit = PlanSubscriptionHelper::getTotalLimits($request->organisation, 'lab');
                if ($labs_limit <= $organisations_lab_limit) {
                    return 'testing';
                }
            }
            $lab_slug = $lab_slug_format = str::slug($request->title);
            $next = 1;
            while (Lab::select('id')->where('slug', '=', $lab_slug)->first()) {
                $lab_slug = "{$lab_slug_format}{$next}";
                $next++;
            }

            $lab = new Lab();
            if ($request->file('image')) {
                $image = FileUploadHelper::uploadImageToS3($request->image, 'lab');
                if ($image == false) {
                    $response = ['success' => false, 'message' => __('responses.fail_organization_image_upload')];

                    return $response;
                }
                $lab->image = $image;
                $lab->mediaType = 'image';
            }
            if ($request->embeddedVideoUrl != '' && $request->coverFileType == 'embeddedCode') {
                $lab->image = $request->embeddedVideoUrl;
                $lab->mediaType = 'embeddedCode';
            }
            if ($request->challenge_switch == '1') {
                $challenge_sequence = '1';
            } else {
                $challenge_sequence = '0';
            }
            if ($request->resource_switch == '1') {
                $resource_sequence = '1';
            } else {
                $resource_sequence = '0';
            }

            if ($request->achievement_en_switch == '1') {
                $achievement_switch = '1';
            } else {
                $achievement_switch = '0';
            }

            $tags = (!empty($request->lab_tags)) ? json_encode($request->lab_tags) : null;
            $lab->user_id = auth()->user()->id;
            $lab->organisation = $request->organisation;
            $lab->tag = $tags;
            $lab->title = $request->title;
            $lab->description = $request->description;
            $lab->category = $request->category;
            $lab->privacy = $request->privacy;
            $lab->cha_sequence = $challenge_sequence;
            $lab->res_sequence = $resource_sequence;
            $lab->enable_achievement = $achievement_switch;
            $lab->member_type = $request->member_type;
            $lab->member = $request->member;
            $lab->country = $request->country;
            $lab->latitute = $request->latitute;
            $lab->longitude = $request->longitude;
            $lab->address = $request->location;
            $lab->city = $request->city;
            $lab->slug = $lab_slug;
            $lab->language = 'en';

            if ($request->labSkill != '') {
                $skills = $request->get('labSkill');
                $lab->lab_skills = $skills;
            }

            if ($request->skillGroups !== null) {
                $skillGroups = implode(',', $request->skillGroups);
            } else {
                $skillGroups = null;
            }
            $lab->skill_groups = $skillGroups;
            if ($request->skillStacks !== null) {
                $skillStacks = implode(',', $request->skillStacks);
            } else {
                $skillStacks = null;
            }
            $lab->skill_stacks = $skillStacks;
            if ($lab->save()) {
                // Assign lab creter as member in member management
                $member_mangement = new MemberManagement();
                $member_mangement->invite_type = 1;
                $member_mangement->module_id = (int) $lab->id;
                $member_mangement->module_type = 1;
                $member_mangement->inviter_id = (int) $lab->user_id;
                //  $member_mangement->email= null;
                $member_mangement->invite_status = 1;
                $member_mangement->email_status = '0';
                $member_mangement->email_resend_count = '0';
                if (!$member_mangement->save()) {
                    return 'error_member_mangement';
                }
                if (!empty($request->link_url) && !empty($request->social_name)) {
                    foreach ($request->link_url as $key => $value) {
                        if (!empty($request->link_url[$key]) && !empty($request->social_name[$key])) {
                            LabSocialLink::create([
                                'user_id'        => auth()->user()->id,
                                'lab_id'         => $lab->id,
                                'social_link_id' => $request->social_name[$key],
                                'link_url'       => $value,
                            ]);
                        }
                    }
                }

                // if ($request->has('lab_tags') && !empty($request->lab_tags)) {
            //     foreach ($request->lab_tags as $value) {
            //         LabTag::create([
            //             'lab_id' => $lab->id,
            //             'tag' => $value,
            //             'user_id' => Auth::user()->id
            //         ]);
            //     }
                // }

                $groups_for_mixpanel = [];

                // Assign lab invited member in member management

                // $request->module_id = $lab->id;
                // $inviteMemberResponce = MemberManagementHelper::inviteMembers($request, 'no');
                // if (!empty($inviteMemberResponce)) {
                //     MemberManagementHelper::setAlertMessage($inviteMemberResponce);
                // }

                // For Notification Release
                $organisation = Organization::where('id', $request->organisation)->first();
                if (!empty($organisation)) {
                    $org_users = FollowersOrganisation::select('user_id')->where('organisation_id', $organisation->id)->where('followers', '1')->get();
                    foreach ($org_users as $fav) {
                        $user = User::find($fav->user_id, ['id', 'name', 'email', 'username', 'is_subscribe', 'device_platform', 'device_token']);
                        if (!empty($user)) {
                            $lab_url = route('details', ['labs', $lab->slug]);
                            $org_url = route('organisationShow', [$organisation->slug]);
                            $send_template_data = [
                                'mail_template'     => 'orglabcreate',
                                'cover_image'       => $organisation->cover_image,
                                'lab_url'           => $lab_url,
                                'org_url'           => $org_url,
                                'username'          => $user->name,
                                'lab'               => $lab->title,
                                'organization_name' => $organisation->name,
                                'challenge'         => $lab->title,
                                'organisation'      => $organisation->name,
                                'email'             => $user->email,
                                'to_email'          => $user->email,
                                'to_name'           => $user->username,
                                'fullname'          => $user->name,
                                'title'             => $organisation->name.'Create Lab', 'subject_title' => $organisation->name.' has just created a new lab',
                                'name'              => $user->name,
                            ];
                            // if (!empty($user->email)) {
                        //     if ($user->is_subscribe == 'subscribe') {
                        //         Event::dispatch('send-template', array($send_template_data));
                        //     }
                            // }

                            // $send_notification = [
                        //     'topic' => $organisation->name . '  has created a lab',
                        //     'message' => 'Hello greetings ' . $organisation->name . '  has created a lab ' . $request->title
                            // ];
                            // if ($user->device_platform != null && $user->device_token != null) {
                        //     if ($user->device_platform == 'Android') {
                        //         Event::dispatch('send-notification', array($user->id, $send_notification));
                        //     } elseif ($user->device_platform == 'IOS') {
                        //         Event::dispatch('send-notification', array($user->id, $send_notification));
                        //     }
                            // }

                            // Send notification for create lab
                            //   NotificationHelper::addNotification(auth()->user()->id, $user->id, 'lab', '0', 'lab_create_notification', '', '', '', '', '', ['organization_name' => $organisation->name,'lab_title' => $request->title]);
                        }
                    }
                }

                if ($request->achievement_en_switch == '1') {
                    if ($request->file('imageAchievement')) {
                        $filename = Str::random(25).'.'.$request->file('imageAchievement')->getClientOriginalExtension();
                        $imageAchievement = Image::make($request->file('imageAchievement'))->resize(625, 355)->stream();
                        Storage::disk('s3')->put('uploads/labs/'.$filename, $imageAchievement);
                        $imageAchievement = 'uploads/labs/'.$filename;
                    }

                    $todo_achievement = (!empty($request->todo_achievement_list)) ? json_encode($request->todo_achievement_list) : $request->todo_achievement_list;
                    LabAchievement::create([
                        'lab_id'                => $lab->id,
                        'achievement_name'      => $request->achievement,
                        'achievement_points'    => $request->awarded,
                        'achievement_condition' => $todo_achievement,
                        'achievement_image'     => $imageAchievement,
                    ]);
                }

                // For associated challenge data
                if (isset($request->selected_challenges) && $request->selected_challenges != '') {
                    $selectedChallenge = explode(',', $request->selected_challenges);
                    $challengeCount = 1;
                    foreach ($selectedChallenge as $challenge_id) {
                        LabChallenges::create([
                            'lab_id'       => $lab->id,
                            'challenge_id' => $challenge_id,
                            'sequence_no'  => $challengeCount,
                        ]);
                        $challengeCount++;
                    }
                }

                // For associated path data
                if (isset($request->selected_paths) && $request->selected_paths != '') {
                    $SelectedPaths = explode(',', $request->selected_paths);
                    $pathCount = 1;
                    foreach ($SelectedPaths as $path_id) {
                        LabChallenges::create([
                            'lab_id'            => $lab->id,
                            'challenge_path_id' => $path_id,
                            'sequence_no'       => $pathCount,
                        ]);
                        $pathCount++;
                    }
                }

                // For associated resource data
                if (isset($request->selected_resource) && $request->selected_resource != '') {
                    $SelectedResource = explode(',', $request->selected_resource);
                    $resourceCount = 1;
                    foreach ($SelectedResource as $resource_id) {
                        LabResources::create([
                            'lab_id'       => $lab->id,
                            'resources_id' => $resource_id,
                            'sequence_no'  => $resourceCount,
                        ]);
                        $resourceCount++;
                    }
                }

                // For associated collection data
                if (isset($request->selected_collection) && $request->selected_collection != '') {
                    $SelectedCollections = explode(',', $request->selected_collection);
                    $collectionCount = 1;
                    foreach ($SelectedCollections as $collection_id) {
                        LabResources::create([
                            'lab_id'        => $lab->id,
                            'collection_id' => $collection_id,
                            'sequence_no'   => $collectionCount,
                        ]);
                        $collectionCount++;
                    }
                }

                // For associated group data
                if (isset($request->selected_groups) && $request->selected_groups != '') {
                    $SelectedResourceCollections = explode(',', $request->selected_groups);
                    $groupCount = 1;
                    foreach ($SelectedResourceCollections as $group_id) {
                        LabResources::create([
                            'lab_id'      => $lab->id,
                            'group_id'    => $group_id,
                            'sequence_no' => $groupCount,
                        ]);
                        $groupCount++;
                    }
                }

                // For User challenge create Function update
                // User::where('id', Auth::user()->id)->update(['is_lab_exist' => 'yes']);

                // Store activity logs
                // activity()->useLog('lab')->performedOn($lab)->causedBy(auth()->user()->id)->withProperties([
            //     'user' => auth()->user(),
            //     'lab' => $lab
                // ])->log($lab->title . ' Added by ' . auth()->user()->name);

                // Mixpanel tracking code: create lab
                // MixpanelHelper::mixpanel_tracking(config('mixpanel.create_lab'), $lab, Auth::user(), $request->ip(), $groups_for_mixpanel);

                // // Env unit testing
                // if ('yes' == \Session::get('unit_test_env', false)) {
            //     return 'Unit test completed!';
                // }

                DB::commit();

                return 'true';
            } else {
                DB::rollBack();

                return 'false';
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return $e;
            abort(500);
        }
    }

    public function checkLabSlug($request)
    {
        try {
            $slug = Lab::where('slug', $request->slug)->first();
            if (!$slug) {
                $response = ['success' => true, 'message' => __('responses.organization_slug_not_exists')];

                return $response;
            }

            return false;
        } catch (\Exception $th) {
            return false;
        }
    }

    public function checkLabName($request)
    {
        try {
            $slug = Lab::where('title', $request->name)->first();
            if (!$slug) {
                $response = ['success' => true, 'message' => __('responses.lab_name_not_exists')];

                return $response;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createform($request)
    {
        $social_name = SocialLink::pluck('name', 'id')->all();
        $todo_achievement_list = LabHelper::getLabAchievementCondition();
        $social_all = SocialLink::where('icon', '!=', null)->get()->toArray();
        $tags = Tag::where('components', 'like', 'lab')->pluck('name', 'id')->all();
        $lab_skills = Skill::pluck('name', 'id')->all();
        $categories = category::Where('components', 'like', '%lab%')->pluck('name', 'id');

        return ['social_links'=>$social_name, 'todo_achievement_list'=>$todo_achievement_list, 'social_all'=>$social_all, 'tags'=>$tags, 'lab_skills'=>$lab_skills, 'categories'=>$categories];
    }

    public function share($id)
    {
        try {
            $total_share = Lab::select('total_share', 'slug')->where('id', $id)->first();
            $new_value = $total_share->total_share + 1;
            $lab = Lab::find($id);
            $lab->total_share = (int) $new_value;
            if ($lab->save()) {
                $response = ['success' => true, 'message' => __('responses.lab_share_message')];

                return $response;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTags($lab_id)
    {
        try {
            $lab_tag = LabTag::get()->where('lab_id', $lab_id);
            if ($lab_tag) {
                $response = ['success' => true, 'data'=>$lab_tag, 'message' => __('responses.lab_tags')];

                return $response;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function labDetail($lab_id)
    {
        try {
            $lab_detailed = Lab::where('id', $lab_id)->first();
            if ($lab_detailed) {
                $response = ['success' => true, 'data'=>$lab_detailed, 'message' => __('responses.lab_detailed_fetech')];

                return $response;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function joinLab($request)
    {
        try {
            $lab = Lab::where('labs.id', (int) $request->lab_id)->with('labUsers')->first();
            if (!empty($lab)) {
                $labMember = MemberManagement::where(['inviter_id' => $lab->labUsers->id, 'module_type' =>'0', 'module_id' => (int) $request->lab_id])->first();

                if (!empty($labMember)) {
                    return response()->json(['status' => 'fail', 'message' => 'You have already sent request to join it.'], 400);
                } else {
                    DB::beginTransaction();
                    $member_mangement = new MemberManagement();
                    $member_mangement->invite_type = 1;
                    $member_mangement->module_id = $request->lab_id;
                    $member_mangement->module_type = '0';
                    $member_mangement->inviter_id = $lab->labUsers->id;
                    $member_mangement->email = null;
                    $member_mangement->invite_status = '0';
                    $member_mangement->email_resend_count = '0';
                    $member_mangement->email_status = '0';
                    if ($member_mangement->save()) {
                        $point = config('points.lab_join');
                        $user_points = new UserPoint();
                        $user_points->type = 'lab_join';
                        $user_points->date = date('Y-m-d');
                        $user_points->user_id = auth()->user()->id;
                        $user_points->point = $point;
                        if ($user_points->save()) {
                            DB::commit();

                            return response()->json(['status' => 'success', 'message' => __('responses.lab_joined_succcess')], 200);
                        }
                        DB::rollBack();
                    } else {
                        DB::rollBack();

                        return response()->json(['status' => 'false', 'message' => __('responses.lab_joined_succcess')], 203);
                    }
                }
            } else {
                DB::rollBack();

                return response()->json(['status' => 'fail', 'message' => __('notification.notification_lab_nf')], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function likeUnlike($request)
    {
        try {
            $is_exists = Favorite::select('id', 'status')->where(['user_id'=>auth()->user()->id, 'action'=>$request->action])->first();
            if (isset($is_exists->id) && !empty($is_exists->id)) {
                if ($is_exists->status == $request->status) {
                    if ($request->status == 0 && $request->action == 1) {
                        return response()->json(['status' => 'success', 'message' =>__('responses.lab_liked_already')], 403);
                    } elseif ($request->status == 1 && $request->action == 1) {
                        return response()->json(['status' => 'success', 'message' =>__('responses.lab_unliked_already')], 403);
                    }
                } else {
                    $favorite = Favorite::find($is_exists->id);
                    $favorite->status = $request->status;
                    if ($favorite->save()) {
                        if ($request->status == 0 && $request->action == 1) {
                            return response()->json(['status' => 'success', 'message' =>__('responses.lab_liked')], 200);
                        } elseif ($request->status == 1 && $request->action == 1) {
                            return response()->json(['status' => 'success', 'message' =>__('responses.lab_unliked')], 200);
                        }
                    }
                }
            } else {
                $data = new Favorite();
                $data->user_id = auth()->user()->id;
                $data->type = $request->type;
                $data->action = $request->action;
                $data->status = $request->status;
                if (isset($request->refence_id) && !empty($request->refence_id)) {
                    $data->refence_id;
                }
                if ($data->save()) {
                    return response()->json(['status' => 'success', 'message' =>__('responses.lab_liked')], 200);
                }
            }

            return response()->json(['status' => 'false', 'message' =>__('responses.lab_unliked')], 503);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function followUnfollow($request)
    {
        try {
            $is_exists = Favorite::select('id', 'status')->where(['user_id'=>auth()->user()->id, 'action'=>$request->action])->first();
            if (isset($is_exists->id) && !empty($is_exists->id)) {
                if ($is_exists->status == $request->status) {
                    if ($request->status == 0 && $request->action == 2) {
                        return response()->json(['status' => 'success', 'message' =>__('responses.lab_followed_already')], 403);
                    } elseif ($request->status == 1 && $request->action == 2) {
                        return response()->json(['status' => 'success', 'message' =>__('responses.lab_unfollowed_already')], 403);
                    }
                } else {
                    $favorite = Favorite::find($is_exists->id);
                    $favorite->status = $request->status;
                    if ($favorite->save()) {
                        if ($request->status == 0 && $request->action == 1) {
                            return response()->json(['status' => 'success', 'message' =>__('responses.lab_followed')], 200);
                        } elseif ($request->status == 1 && $request->action == 1) {
                            return response()->json(['status' => 'success', 'message' =>__('responses.lab_unfollowed')], 200);
                        }
                    }
                }
            } else {
                $data = new Favorite();
                $data->user_id = auth()->user()->id;
                $data->type = $request->type;
                $data->action = $request->action;
                $data->status = $request->status;
                if (isset($request->refence_id) && !empty($request->refence_id)) {
                    $data->refence_id;
                }
                if ($data->save()) {
                    return response()->json(['status' => 'success', 'message' =>__('responses.lab_followed')], 200);
                }
            }

            return response()->json(['status' => 'false', 'message' =>__('responses.lab_unfollowed')], 503);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getSkills($request)
    {
        $getskills = Skill::get();
        if ($getskills) {
            return $getskills;
        } else {
            return false;
        }
    }

    public function updates($request)
    {
        try {
            $lab = Lab::findOrFail($request->id);
            if ($request->embeddedVideoUrl != '' && $request->coverFileType == 'embeddedCode') {
                $lab->image = $request->embeddedVideoUrl;
                $lab->mediaType = 'embeddedCode';
            }

            if ($request->challengeSwitch == '1') {
                $challengeSequence = '1';
            } else {
                $challengeSequence = '0';
            }

            if ($request->resourceSwitch == '1') {
                $resourceSequence = '1';
            } else {
                $resourceSequence = '0';
            }

            if ($request->achievementEnSwitch == '1') {
                $achievementSwitch = '1';
            } else {
                $achievementSwitch = '0';
            }
            $lab->organisation = $request->organisation;
            $lab->title = $request->title;
            $lab->slug = Str::slug($request->slug);
            $lab->description = $request->description;
            $lab->category = $request->category;
            $lab->privacy = $request->privacy;
            $lab->member_type = $request->radioInline;
            $lab->member = $request->member;
            $lab->country = $request->country;
            $lab->latitute = $request->cityLat;
            $lab->longitude = $request->cityLng;
            $lab->address = $request->location;
            $lab->city = $request->city;
            $lab->country = $request->country;
            $lab->cha_sequence = $challengeSequence;
            $lab->res_sequence = $resourceSequence;
            $lab->enable_achievement = $achievementSwitch;
            $lab->lab_skills = $request->labSkill ? $request->labSkill : null;
            if ($lab->save()) {
                return $lab->id;
            }
            dd($lab);
        } catch (\Exception $e) {
            return $e;
        }
    }
}
