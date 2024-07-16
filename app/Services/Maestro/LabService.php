<?php

namespace App\Services\Maestro;

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

    public static function createLab($lab, $organizationId)
    {
        try {
            $cloneLab = $lab->replicate();
            $cloneLab->uuid =Randomize::chars(10)->alphanumeric()->unique()->generate();
            $cloneLab->organization_id=$organizationId;
            $cloneLab->user_id=auth()->user()->id;
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
}
