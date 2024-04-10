<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationEngineHelper
{
    /* -----------------------------------------------------------------------------------------
    @Description:  Function for getting related prepr skills
    -------------------------------------------------------------------------------------------- */
    public static function getRelatedPreprSkills($skills)
    {
        try {
            $endpointUrl = config('ai.skills_recommendation_engine_url');
            $data = ['skills' => $skills];
            $token = config('ai.skills_recommendation_engine_key');

            $response = Http::withHeaders([
                'authorizationToken' => $token,
            ])->post($endpointUrl, $data);

            if ($response->failed()) {
                $responseStatus = false;
            }
            if ($response->serverError()) {
                $responseStatus = false;
            }
            if ($response->clientError()) {
                $responseStatus = false;
            }

            if ($response->getStatusCode() == 200) {
                $decodedResponse = json_decode($response->body(), true);

                if (json_last_error() === JSON_ERROR_NONE && !is_null($decodedResponse)) {
                    $responseStatus = $decodedResponse;
                } else {
                    $responseStatus = $response;
                }
            } else {
                $responseStatus = false;
            }

            return $responseStatus;
        } catch (Exception $e) {
            Log::error('Error in getRelatedPreprSkills in RecommendationEngineHelper.php: '.$e->getMessage());

            return false;
        }
    }
}
