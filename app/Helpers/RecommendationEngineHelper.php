<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Exception;
use Config;

class RecommendationEngineHelper
{
    /* -----------------------------------------------------------------------------------------
    @Description:  Function for getting related prepr skills
    -------------------------------------------------------------------------------------------- */
    public static function getRelatedPreprSkills($url)
    {
        try {
            $response = Http::withHeaders([
                'authorizationToken' => Config::get('app.related_skills_auth_token')
            ])->post($url);

            if ($response->failed()) {
                $responseStatus = false;
            }
            if ($response->serverError()) {
                $responseStatus = false;
            }
            if ($response->clientError()) {
                $responseStatus = false;
            }
            $responseStatus = $response->getStatusCode() == 200 ? json_decode($response->body(), true) : false;

            return $responseStatus;
        } catch (Exception $e) {
            return false;
        }
    }
}
