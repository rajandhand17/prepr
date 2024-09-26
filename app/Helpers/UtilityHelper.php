<?php

namespace App\Helpers;

use App\Models\Discussion;
use App\Services\Manage\ChallengePathService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationCustomizationService;
use App\Services\Manage\OrganizationService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceModuleService;
use App\Services\ProjectService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UtilityHelper
{
    public static function fetchLangaugeISO($languageISO)
    {
        try {
            $language = Language::select('iso')->where('iso', $languageISO)->first();
            if (!empty($language)) {
                return $language['iso'];
            }

            return 'en';
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

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

    public static function generateTitle($titleRequest, $model)
    {
        $title = $title_format = $titleRequest;
        $next = 1;
        while ($model::where('title', '=', $title)->first()) {
            $title = "{$title_format} {$next}";
            $next++;
        }

        return $title;
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
                case 'challenge':
                    $checkComponentSlugExistOrNot = ChallengeService::getChallengeBasedOnSlug($slug);
                    break;
                case 'challenge-path':
                    $checkComponentSlugExistOrNot = ChallengePathService::getChallengePathBasedOnSlug($slug);
                    break;
                case 'resource-module':
                    $checkComponentSlugExistOrNot = ResourceModuleService::getResourceModuleBasedOnSlug($slug);
                    break;
                case 'resource-collection':
                    $checkComponentSlugExistOrNot = ResourceCollectionService::getResourceCollectionBasedOnSlug($slug);
                    break;
                case 'resource-group':
                    $checkComponentSlugExistOrNot = ResourceGroupService::getResourceGroupBasedOnSlug($slug);
                    break;
                case 'project':
                    $checkComponentSlugExistOrNot = ProjectService::getProjectBasedOnSlug($slug);
                    break;
                default:
                    $checkComponentSlugExistOrNot = false;
            }
            if ($checkComponentSlugExistOrNot != false) {
                return $checkComponentSlugExistOrNot;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function formatDateTime($date, $time = 0)
    {
        $carbonDate = Carbon::parse($date);
        //getting preferred TimeZone
        $desiredTimezone = isset(auth()->user()->preferred_timezone) ? auth()->user()->preferred_timezone : 'UTC';
        //getting server timezone
        $defaultTimeZone = config('app.timezone');
        /*set default timezone is utc and convert that according to user timezone*/
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $carbonDate, $defaultTimeZone)
            ->setTimezone($desiredTimezone);
        if ($time == 0) {
            return $date->format('M d, Y H:i:s');
        }

        return $date->format('M d, Y H:i:s');
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

    public static function sanitizeUrl(string $url): string
    {
        if (Str::substr($url, -1) === '/') {
            return substr($url, 0, -1);
        }

        return $url;
    }

    public static function getColumName($iso, $fieldName)
    {
        try {
            if ($iso == 'en') {
                $columName = $fieldName;
            } else {
                $columName = $iso;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName.'_'.$fieldName;
            }

            return $columName;
        } catch (Exception $e) {
            return $fieldName;
        }
    }

    public static function getLabelName($name, $labelName)
    {
        try {
            return $name.' '.$labelName;
        } catch (Exception $e) {
            return $labelName;
        }
    }

    public static function UserIdBasedPreferredOrganization($userData)
    {
        try {
            $getOrganization = false;
            if ($userData->preferred_organization != null) {
                $getOrganization = OrganizationService::getOrganizationExistBasedOnId($userData->preferred_organization);
            }

            return $getOrganization;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function logError($exception)
    {
        $userId = (Auth::id()) ? Auth::id() : null;
        $route = request()->path();
        $ip = request()->ip();
        $time = now();
        $file = $exception->getFile();
        $line = $exception->getLine();

        Log::channel('database')->error($exception->getMessage(), [
            'exception' => $exception,
            'user_id'   => $userId,
            'route'     => $route,
            'ip'        => $ip,
            'time'      => $time,
            'file'      => $file,
            'line'      => $line,
        ]);
    }

    public static function getComponentTotalDiscussions($component, $moduleId)
    {
        try {
            $moduleType = Config('constants.discussion_module_type.'.$component);

            return Discussion::where('module_id', $moduleId)
                ->where('module_type', $moduleType)->count();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return 0;
        }
    }

    public static function isEngLocale(): bool
    {
        return app()->getLocale() === 'en';
    }

    public static function generateURL($component, $slug)
    {
        try {
            $frontEndUrl = self::sanitizeUrl(config('site-settings.frontend_site_url'));
            $componentFrontEndUrl = sprintf('%s/'.$component.'/%s', $frontEndUrl, $slug);

            return $componentFrontEndUrl;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkOrganizationCustomizationData($custom_url)
    {
        try {
            return OrganizationCustomizationService::checkOrganizationCustomizationData($custom_url);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
