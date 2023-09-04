<?php

namespace App\Helpers;

use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UtilityHelper
{
    public static function generateSlug($name, $model)
    {
        $name = preg_replace('/[^A-Za-z0-9\-]/', '-', $name);
        $slug = $slug_format = Str::slug($name);
        $next = 1;
        while ($model::where('slug', '=', $slug)->pluck('title')->first()) {
            $slug = "{$slug_format}-{$next}";
            $next++;
        }

        return $slug;
    }

    public static function checkComponentSlugExistOrNot($component, $slug)
    {
        try {
            $checkComponentSlugExistOrNot = false;
            switch ($component) {
                case 'organization':
                    $checkComponentSlugExistOrNot = OrganizationService::getOrganizationBasedOnSlug($slug);
                    break;
                case 'lab':
                    $checkComponentSlugExistOrNot = LabService::getLabBasedOnSlug($slug);
                    break;
                case 'lab-program':
                    $checkComponentSlugExistOrNot = LabProgramService::getLabProgramBasedOnSlug($slug);
                    break;
                default:
                    $checkComponentSlugExistOrNot = false;
            }
            if ($checkComponentSlugExistOrNot != false) {
                return $checkComponentSlugExistOrNot;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function formatDateTime($date, $time = 0)
    {
        $formatedDate = Carbon::parse($date);
        if ($time == 0) {
            return $formatedDate->format('M d, Y');
        }

        return $formatedDate->format('M d, Y H:i:s');
    }

    public static function validEmail($email)
    {
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode('@', $email);
        $local_array = explode('.', $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
                return false;
            }
        }
        if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode('.', $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!preg_match('/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/', $domain_array[$i])) {
                    return false;
                }
            }
        }

        return true;
    }
}
