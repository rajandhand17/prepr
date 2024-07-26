<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\ComponentAssociation;
use App\Models\Duration;
use App\Models\Lab;
use App\Models\LabSkillsGroupsStack;
use App\Models\Levels;
use App\Models\Organization;
use App\Models\ResourceModule;
use App\Models\Skill;
use App\Models\User;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LabService
{
    public static function getList()
    {
        try {
            $lab_list = Lab::where('labs.status', '1')->where('labs.is_accessible', '1')->get();

            return $lab_list;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabAssociatedItemsById($lab)
    {
        try {
            $skillIds = LabSkillsGroupsStack::where(['lab_id' => $lab->id, 'type' => '0'])->pluck('foreign_id');
            //$labIds = ComponentAssociation::where(['challenge_id' => $lab->id])->pluck('lab_id');
            $moduleIds = ComponentAssociation::where(['lab_id' => $lab->id])->pluck('resource_module_id');

            $organization = !empty($lab->organization_id) ? Organization::where(['id' => $lab->organization_id])->pluck('title', 'id') : null;
            $category = !empty($lab->category_id) ? Category::where(['id' => $lab->category_id])->pluck('title', 'id') : null;
            $level = !empty($lab->level_id) ? Levels::where(['id' => $lab->level_id])->pluck('title', 'id') : null;
            $duration = !empty($lab->duration_id) ? Duration::where(['id' => $lab->duration_id])->pluck('title', 'id') : null;
            $user = !empty($lab->user_id) ? User::where(['id' => $lab->user_id])->pluck('username', 'id') : null;
            $skills = !empty($lab->id) ? Skill::whereIn('id', $skillIds)->pluck('title', 'id') : null;
            //  $labs = !empty($lab->user_id) ? Lab::whereIn('id', $labIds)->pluck('title', 'id') : null;
            $resourceModules = !empty($lab->user_id) ? ResourceModule::whereIn('id', $moduleIds)->pluck('title', 'uuid') : null;

            return ['category' => $category ?? [], 'organization' => $organization, 'skills' => $skills, 'skillIds' => $skillIds, 'user' => $user, 'level' => $level, 'duration' => $duration, 'resourceModules' => $resourceModules, 'moduleIds' => $moduleIds];
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    public static function createLab($request)
    {
        try {
            $labs_image = null;
            if (request('image')) {
                $filename = Str::random(25).'.'.$request->file('image');
                // $image = Image::make($request->file('image'))->resize(625, 355)->stream();
                $img = Storage::disk('s3')->put('uploads/labs/'.$filename, $request->file('image'));
                $image = 'uploads/labs/'.$filename;

                $labs_image = $image;
            }
            $privacy = config('constants.lab_privacy.no');
            switch ($request->privacy) {
                case 'yes':
                    $privacy = config('constants.lab_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.lab_privacy.no');
                    break;
                default:
                    $privacy = config('constants.lab_privacy.yes');
                    break;
            }
            $lab = new Lab();
            $lab->user_id = $request->user_id;
            $lab->organization_id = $request->organization_id;
            $slug = UtilityHelper::generateSlug($request->title, $lab);
            $lab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $lab->title = $request->title;
            $lab->slug = $slug;
            $lab->is_verified = '1';
            $lab->duration_id = $request->duration;
            $lab->level_id = $request->level;
            $lab->description = $request->description;
            $lab->category_id = $request->category;
            $lab->privacy = $privacy;
            $lab->status = '1';

            // $cover_image = $lab->image;
            $lab->language = $request->language;
            $lab->save();

            return $lab;
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    public static function createCloneLab($lab, $organizationId)
    {
        try {
            $cloneLab = $lab->replicate();
            $cloneLab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $cloneLab->organization_id = $organizationId;
            $cloneLab->user_id = auth()->user()->id;
            $cloneLab->save();

            return $cloneLab;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabBasedOnOrganization($request)
    {
        try {
            $labs = Lab::select('id', 'title')->orderBy('id', 'DESC')->where('organization_id', $request->org_id);
            if ($request->search) {
                $labs = $labs->where('title', 'LIKE', '%'.$request->search.'%');
            }
            $labs = $labs->where('language', $request->language)->get()->take(20)->pluck('title', 'id');
            $count = 0;
            $json_stacks = $json_result = [];
            foreach ($labs as $key => $lab_to_return) {
                $json_stacks[$count]['id'] = $key;
                $json_stacks[$count]['text'] = $lab_to_return;
                $count++;
            }
            $json_result['result'] = $json_stacks;

            return response()->json($json_result);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabsWithRelatedTables($request)
    {
        try {
            $lab = Lab::with('skills', 'address', 'tags', 'external_links', 'achievement')->where('id', $request->lab)->first();

            return $lab;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLabCounts()
    {
        try {
            return Lab::count();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateLabById($request, $id)
    {
        try {
            $lab = Lab::findOrFail($id);
            if (!empty($lab)) {
                $input = $request->all();

                $cover_image = '';

                $input = $request->except('cover_image', 'people_name', 'user_name', 'user_role', 'org_social', 'social_url');
                if ($request->file('cover_image')) {
                    $cover_image = $request->file('cover_image')->store('uploads/lab', 's3');
                    $lab->cover_image = $cover_image ? $cover_image : 'NULL';
                    $lab->save();
                }

                $privacy = $lab->privacy;
                if ($request->has('privacy')) {
                    switch ($request->privacy) {
                        case 'yes':
                            $privacy = config('constants.lab_privacy.yes');
                            break;
                        case 'no':
                            $privacy = config('constants.lab_privacy.no');
                            break;
                        default:
                            $privacy = config('constants.lab_privacy.yes');
                            break;
                    }
                }
                $lab->privacy = $privacy;
                $lab->user_id = $request->user_id;
                $lab->title = $request->title;
                $lab->description = $request->description;
                $lab->category_id = $request->category;
                $lab->save();

                return true;
            }

            return false;
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    public static function deleteLab($id)
    {
        try {
            $lab = Lab::find($id);
            if ($lab) {
                return $lab->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getLab($action, $labId)
    {
        try {
            $lab = Lab::select('title', 'id');
            if ($action == 'edit') {
                $lab = $lab->where(['id' => $labId]);
            }

            return $lab->pluck('title', 'id');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getLabs($request)
    {
        try {
            $labs = Lab::select('id', 'title')->orderBy('id', 'DESC');
            if ($request->search) {
                $labs = $labs->where('title', 'LIKE', '%'.$request->search.'%');
            }
            $labs = $labs->get()->take(20)->pluck('title', 'id');
            $count = 0;
            $json_stacks = $json_result = [];
            foreach ($labs as $key => $lab_to_return) {
                $json_stacks[$count]['id'] = $key;
                $json_stacks[$count]['text'] = $lab_to_return;
                $count++;
            }
            $json_result['result'] = $json_stacks;

            return response()->json($json_result);
        } catch (Exception $e) {
            return false;
        }
    }
}
