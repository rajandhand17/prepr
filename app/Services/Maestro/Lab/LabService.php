<?php

namespace App\Services\Maestro\Lab;

use App\Models\Lab;
use HiFolks\RandoPhp\Randomize;

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

    public static function getLabById($labId)
    {
        try {
            $lab_list = Lab::where('labs.id', $labId)->first();

            return $lab_list;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function createLab($labDetails, $organizationId)
    {
        try {
            $lab = new Lab();
            $lab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $lab->language = $labDetails->language;
            $lab->user_id = auth()->user()->id;
            $lab->organization_id = $organizationId;
            $lab->category_id = $labDetails->category_id;
            $lab->duration_id = $labDetails->duration_id;
            $lab->level_id = $labDetails->level_id;
            $lab->type = $labDetails->type;
            $lab->slug = $labDetails->slug;
            $lab->title = $labDetails->title;
            $lab->description = $labDetails->description;
            $lab->privacy = $labDetails->privacy;
            $lab->media_type = $labDetails->media_type;
            $lab->media = $labDetails->media;
            $lab->status = $labDetails->status;
            $lab->total_share = $labDetails->total_share;
            $lab->is_auto_created = $labDetails->is_auto_created;
            $lab->is_ai_created = $labDetails->is_ai_created;
            $lab->is_resource_sequential = $labDetails->is_resource_sequential;
            $lab->is_sequential = $labDetails->is_sequential;
            $lab->is_achievement_enabled = $labDetails->is_achievement_enabled;
            $lab->is_notification_enabled = $labDetails->is_notification_enabled;
            $lab->is_verified = $labDetails->is_verified;
            $lab->is_live_event_enabled = $labDetails->is_live_event_enabled;
            $lab->save();

            return $lab;
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
            if ($request->privacy == 'public') {
                $labs = $labs->where('privacy', $request->privacy);
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
        } catch (Exception $e) {
            return false;
        }
    }
}
