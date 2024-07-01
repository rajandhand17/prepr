<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class WikipediaHelper
{
    public static function fetchSkillDescription($skillName, $language)
    {
        try {
            $wikipediaUrl = ($language == 'fr-CA') ? config('wikipedia.FRENCH_WIKIPEDIA_URL') : config('wikipedia.ENGLISH_WIKIPEDIA_URL');
            $client = new Client();
            $wikipedia_description_response = $client->request('GET', $wikipediaUrl, [
                'query' => [
                    'format'      => 'json',
                    'action'      => 'query',
                    'prop'        => 'extracts',
                    'exintro'     => 1,
                    'exchars'     => 400,
                    'explaintext' => 1,
                    'redirects'   => 1,
                    'titles'      => $skillName,
                ],
            ]);
            if (!$wikipedia_description_response) {
                return false; //Failed to fetch Wikipedia description
            }
            $decodedResponse = json_decode($wikipedia_description_response->getBody(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return false; //Invalid JSON response from Wikipedia
            }
            $job_description = isset($decodedResponse['query']['pages']) ? current($decodedResponse['query']['pages'])
                : '';
            if (isset($job_description['missing'])) {
                $job_description = $skillName;
            } else {
                $job_description = $job_description['extract'] ?? $skillName;
            }
            if ($job_description == $skillName) {
                $job_description = __('responses.no_description');
            }

            return $job_description;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchRelatedSkills($url)
    {
        try {
            $client = new Client();
            $response = $client->post($url, [
                'headers' => [
                    'authorizationToken' => config('wikipedia.RELATED_SKILLS_AUTH_TOKEN'),
                ],
            ]);
            if (!$response) {
                $responseStatus = false;
            }
            $responseStatus = $response->getStatusCode() == 200 ? json_decode($response->getBody(), true) : false;

            return $responseStatus;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
