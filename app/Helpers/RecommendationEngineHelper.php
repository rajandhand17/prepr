<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

class RecommendationEngineHelper
{
    /* -----------------------------------------------------------------------------------------
    @Description:  Function for getting related prepr skills
    -------------------------------------------------------------------------------------------- */
    public static function getRelatedPreprSkills($url)
    {
        try {
            $response = Http::withHeaders([
                'authorizationToken' => config('app.related_skills_auth_token')
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
            Log::error("Error in getRelatedPreprSkills in RecommendationEngineHelper.php: " . $e->getMessage());
            return false;
        }
    }
}
