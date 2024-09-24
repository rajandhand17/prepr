<?php

namespace App\Helpers;

use App\Models\Challenge;
use App\Models\Lab;
use App\Services\Manage\CampusConnectOpportunityService;
use App\Services\Manage\CampusConnectStoryService;
use App\Services\SkillService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CampusConnectHelper
{
    private static function getEndpoint($url)
    {
        try {
            return self::sanitizeUrl(config('campus-connect.campus_connect_api_url', '')).$url;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function getCampusConnectAuthorizationHeader()
    {
        try {
            /** SECRET KEY PROVIDED BY CAMPUS CONNECT */
            $campusConnectSecret = config('campus-connect.campus_connect_secret', '');
            /** TIMESTAMP FOR WHEN THE SIGNATURE IS VALID*/
            $timeStamp = (string) time();
            /** GENERATING UNIQUE SIGNATURE AS PER THE TIMESTAMP - GUIDED BY CAMPUS CONNECT DOCS */
            $sha = hash_hmac('sha256', $timeStamp, $campusConnectSecret, true);
            $signature = base64_encode($sha);

            return [
                'x-api-key'   => config('campus-connect.campus_connect_key', ''),
                'x-timestamp' => $timeStamp,
                'x-signature' => $signature,
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function get($url, $params = [], $headers = [])
    {
        try {
            $campusConnectAuthorizationHeader = self::getCampusConnectAuthorizationHeader();
            $mergedHeader = array_merge($campusConnectAuthorizationHeader, $headers ?: []);
            $url = self::getEndpoint($url);

            return Http::withHeaders($mergedHeader)->get($url, $params);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function post($url, $data, $headers = [])
    {
        try {
            $campusConnectAuthorizationHeader = self::getCampusConnectAuthorizationHeader();
            $mergedHeader = array_merge($campusConnectAuthorizationHeader, $headers ?: []);
            $url = self::getEndpoint($url);

            return Http::withHeaders($mergedHeader)->post($url, $data);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function put($url, $data, $headers = [])
    {
        try {
            $campusConnectAuthorizationHeader = self::getCampusConnectAuthorizationHeader();
            $mergedHeader = array_merge($campusConnectAuthorizationHeader, $headers ?: []);
            $url = self::getEndpoint($url);

            return Http::withHeaders($mergedHeader)->put($url, $data);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function listSchools()
    {
        try {
            $response = self::get('/schools');
            if ($response->status() >= 400) {
                return false;
            }

            return $response->json();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function updateOrCreateOpportunity($data)
    {
        try {
            $response = self::post('/opportunities', $data);
            if ($response != false) {
                if ($response->status() === 409) {
                    $updateResponse = self::put('/opportunities', $data);
                    if ($updateResponse->status() >= 400) {
                        return false;
                    }

                    return true;
                }

                if ($response->status() >= 400) {
                    return false;
                }
            }

            return true;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function updateOrCreateStory($data)
    {
        try {
            $response = self::post('/curatedContent', $data);
            if ($response->status() === 409) {
                $updateResponse = self::put('/curatedContent', $data);
                if ($updateResponse->status() >= 400) {
                    return false;
                }

                return true;
            }

            if ($response->status() >= 400) {
                return false;
            }

            return true;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function sanitizeUrl($url): string
    {
        try {
            if (Str::substr($url, -1) === '/') {
                return substr($url, 0, -1);
            }

            return $url;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function generateUrl($model, $slug)
    {
        try {
            $url = self::sanitizeUrl(config('site-settings.frontend_site_url'));
            switch ($model) {
                case Challenge::class:
                    return sprintf('%s/challenge/%s', $url, $slug);
                case Lab::class:
                    return sprintf('%s/labs/%s', $url, $slug);
                default:
                    return '';
            }
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function serializeOpportunityData($data)
    {
        return [
            'epId'    => data_get($data, 'id'),
            'schools' => data_get($data, 'schools'),
            'company' => [
                'id'         => data_get($data, 'company.id'),
                'name'       => data_get($data, 'company.name', '-'),
                'address1'   => data_get($data, 'company.address'),
                'address2'   => null,
                'city'       => data_get($data, 'company.city'),
                'province'   => data_get($data, 'company.state'),
                'country'    => null,
                'postalCode' => null,
                'phone'      => null,
                'website'    => null,
                'naic'       => null,
            ],
            'contact' => [
                'firstName' => data_get($data, 'contact.first_name'),
                'lastName'  => data_get($data, 'contact.last_name'),
                'email'     => data_get($data, 'contact.email'),
            ],
            'language'              => self::isEngLocale() ? 'en' : 'fr',
            'type'                  => data_get($data, 'type'),
            'position'              => self::isEngLocale() ? data_get($data, 'position') : 'N/A',
            'position_fr'           => self::isEngLocale() ? 'N/A' : data_get($data, 'position'),
            'numberOfPosition'      => data_get($data, 'no_of_position'),
            'city'                  => data_get($data, 'city'),
            'province'              => data_get($data, 'province'),
            'description'           => self::isEngLocale() ? data_get($data, 'description') : 'N/A',
            'description_fr'        => self::isEngLocale() ? 'N/A' : data_get($data, 'description'),
            'qualifications'        => self::isEngLocale() ? data_get($data, 'qualifications') : 'N/A',
            'qualifications_fr'     => self::isEngLocale() ? 'N/A' : data_get($data, 'qualifications'),
            'SkillsHighlights'      => self::isEngLocale() ? data_get($data, 'skill_highlights') : 'N/A',
            'SkillsHighlights_Fr'   => self::isEngLocale() ? 'N/A' : data_get($data, 'skill_highlights'),
            'educationRequirements' => [
                [
                    'level'        => data_get($data, 'education.level'),
                    'studyYears'   => data_get($data, 'education.study_years'),
                    'subjectAreas' => data_get($data, 'education.subject_areas'),
                ],
            ],
            'dateDeadline'               => data_get($data, 'deadline'),
            'isPaidOpportunity'          => data_get($data, 'is_paid_opportunity'),
            'salaryAmount'               => data_get($data, 'salary_amount'),
            'salaryPaymentFrequency'     => data_get($data, 'salary_payment_frequency'),
            'hoursPerWeek'               => data_get($data, 'hours_per_week'),
            'applicationInstructions'    => self::isEngLocale() ? data_get($data, 'application_instructions') : 'N/A',
            'applicationInstructions_fr' => self::isEngLocale() ? 'N/A' : data_get($data, 'application_instructions'),
            'preferredResponse'          => data_get($data, 'preferred_response'),
            'atsLink'                    => data_get($data, 'ats_link'),
            'applicationEmail'           => data_get($data, 'application_email'),
        ];
    }

    public static function serializeStoryData($data)
    {
        try {
            $formatted = [
                'id'             => data_get($data, 'id'),
                'type'           => 'BASIC',
                'title'          => self::isEngLocale() ? data_get($data, 'title') : 'N/A',
                'fr_title'       => self::isEngLocale() ? 'N/A' : data_get($data, 'title'),
                'body'           => self::isEngLocale() ? data_get($data, 'body') : 'N/A',
                'fr_body'        => self::isEngLocale() ? 'N/A' : data_get($data, 'body'),
                'companyName'    => self::isEngLocale() ? data_get($data, 'company_name') : 'N/A',
                'fr_companyName' => self::isEngLocale() ? 'N/A' : data_get($data, 'company_name'),
                'website'        => data_get($data, 'website'),
                'learn_more'     => data_get($data, 'website'),
                'media_type'     => data_get($data, 'media_type'),
            ];

            $mediaType = data_get($formatted, 'media_type');
            if ($mediaType === 'IMAGE') {
                $formatted['image'] = [
                    'file'        => data_get($data, 'image.file_url'),
                    'title'       => data_get($data, 'image.title'),
                    'description' => data_get($data, 'image.description'),
                ];
            } elseif ($mediaType === 'VIDEO') {
                $formatted['video'] = [
                    'youtube_id'  => data_get($data, 'video.youtube_id'),
                    'youtube_url' => data_get($data, 'video.youtube_url'),
                ];
            }

            $formatted['requirements'] = [
                'schools' => data_get($data, 'requirements.schools'),
            ];

            return $formatted;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function prepareOpportunityData($id, $skillIds, $slug, $model, $data, $user, $organization)
    {
        try {
            $skills = SkillService::getSkillBasedOnIds($skillIds)->pluck('title')->toArray();

            $eid = $id;

            /**
             * TO AVOID ID COLLISION WHEN CAMPUS CONNECT IS LINKED WITH DIFFERENT ENTITIES.
             */
            $existing = CampusConnectOpportunityService::findByModelTypeAndId($model, $id);

            if ($existing) {
                $eid = data_get($existing, 'ep_id', $id);
            } else {
                $other = CampusConnectOpportunityService::findByEpId($id);
                if ($other) {
                    $largestId = CampusConnectOpportunityService::findByLargestEpId();
                    if ($largestId) {
                        $eid = data_get($largestId, 'ep_id') + 1;
                    }
                }
            }

            return [
                'id'      => $eid,
                'schools' => data_get($data, 'campus_connect_schools', []),
                'company' => [
                    'id'      => data_get($organization, 'id'),
                    'name'    => data_get($organization, 'display_name') ?? data_get($organization, 'title', '-'),
                    'address' => data_get($organization, 'address.0.full_address', '-'),
                    'city'    => data_get($organization, 'address.0.city', '-'),
                    'state'   => data_get($organization, 'address.0.state', '-'),
                ],
                'contact' => [
                    'first_name' => data_get($user, 'first_name'),
                    'last_name'  => data_get($user, 'last_name'),
                    'email'      => data_get($user, 'email'),
                ],
                'type'             => data_get($data, 'campus_connect_job_type'),
                'position'         => data_get($data, 'campus_connect_job_title'),
                'no_of_position'   => data_get($data, 'campus_connect_no_of_position'),
                'city'             => data_get($data, 'campus_connect_city'),
                'province'         => data_get($data, 'campus_connect_province'),
                'description'      => data_get($data, 'campus_connect_description'),
                'qualifications'   => data_get($data, 'campus_connect_qualification'),
                'skill_highlights' => implode(',', $skills),
                'education'        => [
                    'level'         => data_get($data, 'campus_connect_education_level'),
                    'study_years'   => data_get($data, 'campus_connect_education_study_years'),
                    'subject_areas' => data_get($data, 'campus_connect_education_subject_areas'),
                ],
                'deadline'                 => data_get($data, 'campus_connect_deadline'),
                'hours_per_week'           => data_get($data, 'campus_connect_hours_per_week'),
                'is_paid_opportunity'      => true,
                'salary_amount'            => data_get($data, 'campus_connect_salary_amount'),
                'salary_payment_frequency' => data_get($data, 'campus_connect_salary_payment_frequency'),
                'application_instructions' => data_get($data, 'campus_connect_application_instructions'),
                'preferred_response'       => data_get($data, 'campus_connect_preferred_response'),
                'application_email'        => data_get($data, 'campus_connect_application_email'),
                'ats_link'                 => CampusConnectHelper::generateUrl($model, $slug),
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function prepareStoryData($id, $slug, string $model, array $data, $organization)
    {
        try {
            $imageUrl = data_get($data, 'campus_connect_story_image');

            if (data_get($data, 'campus_connect_has_image_file') == 'true' && request()->hasFile('campus_connect_story_image')) {
                $file = request()->file('campus_connect_story_image');
                $imageUrl = FileUploadHelper::uploadImageToS3($file, 'campus_connect');
            }

            $oldImage = data_get($data, 'campus_connect_story_old_image');
            /** RAW DATA WHICH IS LATER MAPPED BY THE SERIALIZER */
            if ($imageUrl) {
                $fileUrl = $imageUrl;
            } else {
                $fileUrl = $oldImage;
            }

            if ($fileUrl && filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
                $fileUrl = config('site-settings.aws_url').$fileUrl;
            }

            $eid = $id;
            /**
             * TO AVOID ID COLLISION WHEN CAMPUS CONNECT IS LINKED WITH DIFFERENT ENTITIES.
             */
            $existing = CampusConnectStoryService::findByModelTypeAndId($model, $id);

            if ($existing) {
                $eid = data_get($existing, 'ep_id', $id);
            } else {
                $other = CampusConnectStoryService::findByEpId($id);
                if ($other) {
                    $largestId = CampusConnectStoryService::findByLargestEpId();
                    if ($largestId) {
                        $eid = data_get($largestId, 'ep_id') + 1;
                    }
                }
            }

            return [
                'id'           => $eid,
                'title'        => data_get($data, 'campus_connect_story_title'),
                'body'         => data_get($data, 'campus_connect_story_body'),
                'company_name' => data_get($organization, 'display_name') ?? data_get($organization, 'title', '-'),
                'website'      => self::generateUrl($model, $slug),
                'media_type'   => data_get($data, 'campus_connect_story_media_type'),
                'image'        => [
                    'has_image_file' => data_get($data, 'campus_connect_has_image_file'),
                    'file_url'       => $fileUrl,
                    'file'           => $imageUrl ?: $oldImage,
                    'title'          => data_get($data, 'campus_connect_story_image_title'),
                    'description'    => data_get($data, 'campus_connect_story_image_description'),
                ],
                'video' => [
                    'youtube_url' => data_get($data, 'campus_connect_story_video_youtube_url'),
                ],
                'requirements' => [
                    'schools' => data_get($data, 'campus_connect_schools'),
                ],
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function isEngLocale()
    {
        try {
            return request()->language === 'en';
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
