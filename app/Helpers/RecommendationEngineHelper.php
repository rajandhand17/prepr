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

            if ($response->failed() || $response->serverError() || $response->clientError()) {
                return false;
            }

            if ($response->getStatusCode() == 200) {
                $jsonString = str_replace('NaN', 'null', $response->body());
                $decodedResponse = json_decode($jsonString, true);

                if (json_last_error() === JSON_ERROR_NONE && !is_null($decodedResponse)) {
                    // Filter out key-value pairs where any value is null
                    foreach ($decodedResponse as $key => $value) {
                        if (is_array($value) && in_array(null, $value, true)) {
                            unset($decodedResponse[$key]);
                        }
                    }

                    return $decodedResponse;
                }
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in getRelatedPreprSkills in RecommendationEngineHelper.php: '.$e->getMessage());

            return false;
        }
    }
}
