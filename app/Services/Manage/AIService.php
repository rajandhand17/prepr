<?php

namespace App\Services\Manage;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Exception;
use Config;

class AIService
{
    // protected $gptClient;
    // protected $bingArticleClient;
    // protected $bingVideoClient;
    // protected $language;

    // public function __construct()
    // {
    //     $gptApiKey = Config::get('app.OPENAI_API_KEY');
    //     $bingApiKey = Config::get('app.BING_API_KEY');
    //     $this->gptClient = new Client([
    //         'base_uri' => 'https://api.openai.com/v1/chat/completions',
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //             'Authorization' => 'Bearer ' . $gptApiKey,
    //         ],
    //     ]);

    //     $this->bingArticleClient = new Client([
    //         'base_uri' => 'https://api.bing.microsoft.com/v7.0/search',
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //             'Ocp-Apim-Subscription-Key' => $bingApiKey,
    //         ],
    //     ]);

    //     $this->bingVideoClient = new Client([
    //         'base_uri' => 'https://api.bing.microsoft.com/v7.0/videos/search',
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //             'Ocp-Apim-Subscription-Key' => $bingApiKey,
    //         ],
    //     ]);

    //     $this->language = App::currentLocale();
    // }

    public static function createChallengeUsingAI($request, $upload_cover_image)
    {
        try {
            Log::info($request);
            Log::info($upload_cover_image);
        } catch (Exception $e) {
            return false;
        }
    }
    // public function generateChallenge($request)
    // {
    //     $language = $this->language;
    //     $gptClient = $this->gptClient;

    //     $selectedJobs = $request->input('selectedJobs');
    //     $difficultyLevelName = $request->input('difficultyLevelName');
    //     $categories = $request->input('categories');
    //     $challengeDurationName = $request->input('challengeDurationName');

    //     if ($language == "en") {
    //         $response = $gptClient->post('', [
    //             'json' => [
    //                 'model' => 'gpt-3.5-turbo',
    //                 'n' => 3,
    //                 'messages' => [
    //                     [
    //                         'role' => 'user',
    //                         'content' => 'Please design an educational challenge for the career: "' . $selectedJobs . '", at the level: "' . $difficultyLevelName . '", for the duration of "' . $challengeDurationName . '" that it takes for the challenge to finish.
    //                         1. **Title**: Craft a brief title for the challenge.
    //                         2. **Description**: Provide a brief description about the challenge and a detailed, step-by-step guide in HTML format suitable for online implementation. Make sure the description is complete and is not cut off. Do it exactly as shown in the example.
    //                         3. **Steps**: Also write the exact same steps in an array.
    //                         4. **Skills**: Enumerate 10 vital skills necessary for this challenge.
    //                         5. **Category**: Based on the specified career and level, select one category from: "' . $categories . '".
    //                         6. **Reflections**: provide 3-5 reflective questions that participants can answer after completing the challenge. These questions should help participants reflect on their approach to the challenge, the skills they applied, any roadblocks they encountered, and their overall learning experience.

    //                         Output format:
    //                         {
    //                         "title": "Challenge Title",
    //                         "description": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p>...",
    //                         "category": "Selected Category",
    //                         "steps": ["Step 1", "Step 2", ...],
    //                         "skills": ["Skill 1", "Skill 2", ...],
    //                         "reflections": ["Reflection 1", "Reflection 2", ...]
    //                         }'
    //                     ]
    //                 ],
    //             ],
    //         ]);
    //     } elseif ($language == "fr-CA") {
    //         $response = $gptClient->post('', [
    //             'json' => [
    //                 'model' => 'gpt-3.5-turbo',
    //                 'n' => 3,
    //                 'messages' => [
    //                     [
    //                         'role' => 'user',
    //                         'content' => 'Please design an educational challenge for the career: "' . $selectedJobs . '", at the level: "' . $difficultyLevelName . '", for the duration of "' . $challengeDurationName . '" that it takes for the challenge to finish. The title, description, and category should be provided in French. The list of skills should be provided in English. 
    //                         1. **Title**: Craft a brief title for the challenge in French.
    //                         2. **Description**: Provide a brief description about the challenge and a detailed, step-by-step guide in HTML format suitable for online implementation, in French. Make sure the description is complete and is not cut off.
    //                         3. **Steps**: Also write the exact same steps in an array in French.
    //                         4. **Skills**: Enumerate 10 vital skills necessary for this challenge, in English. Remember, the skills have to be in English language
    //                         5. **Category**: Based on the specified career and level, select one category from: "' . $categories . '", in French.
    //                         6. **Reflections**: provide 3-5 reflective questions that participants can answer after completing the challenge. These questions should help participants reflect on their approach to the challenge, the skills they applied, any roadblocks they encountered, and their overall learning experience, write it in French.

    //                         Output format:
    //                         {
    //                         "title": "Titre du Défi",
    //                         "description": "<p>Brève description du défi</p><br /><p>1. Étape initiale.</p><p>2. Étape suivante.</p>...",
    //                         "category": "Catégorie Sélectionnée",
    //                         "steps": ["Étape 1", "Étape 2", ...],
    //                         "skills": ["Skill 1", "Skill 2", ...],
    //                         "reflections": ["Réflexion 1", "Réflexion 2", ...]
    //                         }'
    //                     ]
    //                 ],
    //             ],
    //         ]);
    //     }

    //     return json_decode($response->getBody(), true);
    // }

    // public function generateResourceModule($request)
    // {
    //     $language = $this->language;
    //     $languageName = ($language == "en") ? "English" : "French";
    //     $bingArticleClient = $this->bingArticleClient;
    //     $bingVideoClient = $this->bingVideoClient;

    //     $title = $request->input('title');

    //     $articleResponse = $bingArticleClient->request('GET', '', [
    //         'query' => ['q' => 'Articles about ' . $title . '', 'count' => 6,],
    //     ]);

    //     $videoResponse = $bingVideoClient->request('GET', '', [
    //         'query' => ['q' => 'Videos about ' . $title . '', 'count' => 6,],
    //     ]);

    //     $jsonArticleResponse = json_decode($articleResponse->getBody(), true);

    //     $articles = [];

    //     // Check if 'webPages' and 'value' keys exist in the array
    //     if (isset($jsonArticleResponse['webPages']) && isset($jsonArticleResponse['webPages']['value'])) {
    //         // Iterate through each item in the 'value' array
    //         foreach ($jsonArticleResponse['webPages']['value'] as $item) {
    //             // Initialize an associative array for each item
    //             $itemDetails = [];

    //             // Check if 'name' key exists and add it to the associative array
    //             if (isset($item['name'])) {
    //                 $itemDetails['name'] = $item['name'];
    //             }

    //             // Check if 'displayUrl' key exists and add it to the associative array
    //             if (isset($item['displayUrl'])) {
    //                 $itemDetails['displayUrl'] = $item['displayUrl'];
    //             }

    //             // Add the associative array to the $displayUrls array if it contains any data
    //             if (!empty($itemDetails)) {
    //                 $articles[] = $itemDetails;
    //             }
    //         }
    //     }

    //     $jsonVideoResponse = json_decode($videoResponse->getBody(), true);

    //     $videos = [];

    //     // Check if 'value' key exists in the array
    //     if (isset($jsonVideoResponse['value'])) {
    //         // Iterate through each item in the 'value' array
    //         foreach ($jsonVideoResponse['value'] as $item) {
    //             // Check if 'embedHtml' key exists and add it to the array
    //             if (isset($item['embedHtml'])) {
    //                 $videos[] = $item['embedHtml'];
    //             }
    //         }
    //     }

    //     // Initialize an array to store the values
    //     $mergedArray = [
    //         'articles' => $articles,
    //         'videos' => $videos
    //     ];

    //     return $mergedArray;
    // }
}
