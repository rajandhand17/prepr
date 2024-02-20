<?php

namespace App\Services\Manage;

use App\Helpers\RecommendationEngineHelper;
use App\Models\Category;
use App\Models\Duration;
use App\Models\Job;
use App\Models\Levels;
use App\Models\Skill;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Exception;
use Config;

class AIService
{
    protected $openAIClient;
    protected $bingArticleClient;
    protected $bingVideoClient;
    protected $skillsRecommendationEngineUrl;
    protected $relatedSkillsAuthToken;

    public function __construct()
    {
        $openAIAPIKey = env('OPENAI_API_KEY');
        $bingAPIKey = env('BING_API_KEY');

        $this->openAIClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/chat/completions',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $openAIAPIKey,
            ],
        ]);

        $this->skillsRecommendationEngineUrl = config('app.skills_recommendation_engine_url');
        $this->relatedSkillsAuthToken = config('app.related_skills_auth_token');


        $this->bingArticleClient = new Client([
            'base_uri' => 'https://api.bing.microsoft.com/v7.0/search',
            'headers' => [
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $bingAPIKey,
            ],
        ]);

        $this->bingVideoClient = new Client([
            'base_uri' => 'https://api.bing.microsoft.com/v7.0/videos/search',
            'headers' => [
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $bingAPIKey,
            ],
        ]);
    }

    public function createChallengeUsingAI($request)
    {
        try {
            $attempt = 0;
            $validChallenges = [];
            $irrelevantDataCount = 0;

            // $language = $request->language;

            $jobTitles = implode(', ', Job::whereIn('id', $request->jobs)->get()->pluck('title')->toArray());

            $skillTitles = implode(', ', Skill::whereIn('id', $request->skills)->get()->pluck('title')->toArray());

            $durationTitle = Duration::find($request->duration_id)->title;

            $levelTitle = Levels::find($request->level_id)->title;

            $additionalInformation = $request->additional_information;

            $categoryTitles = Category::pluck('title')->implode(', ');

            while ($attempt < 3 && count($validChallenges) < 2) {
                Log::info('Attempt: ' . $attempt + 1);
                $attempt++;

                $startTimeAPI = microtime(true);

                $openAIResponse = $this->fetchChallengesFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles);

                $endTimeAPI = microtime(true);
                Log::info('API call duration: ' . ($endTimeAPI - $startTimeAPI) . ' seconds');


                if (!$openAIResponse || empty($openAIResponse['choices'])) {
                    continue; // Skip this iteration if the response is empty or invalid
                }

                $startTimeValidatingChallenges = microtime(true);

                foreach ($openAIResponse['choices'] as $choice) {
                    $content = json_decode($choice['message']['content'], true);

                    // Validate the challenge content
                    if ($content === "Irrelevant given data!" || empty($content['skills'])) {
                        $irrelevantDataCount++;
                        continue; // Skip this challenge if content is irrelevant or lacks skills
                    }

                    // Process and update skills based on recommendations
                    $startTimeValidatingSkills = microtime(true);

                    $updatedSkills = $this->processSkills($content['skills']);

                    $endTimeValidatingSkills = microtime(true);
                    Log::info('Validating skills duration: ' . ($endTimeValidatingSkills - $startTimeValidatingSkills) . ' seconds');

                    if (count($updatedSkills) < 6 || !$content['title']) {
                        Log::error('Challenge Failed!');
                        continue; // Skip this challenge if it has less than 6 valid skills after processing
                    }

                    $content['level'] = $levelTitle;
                    $content['duration'] = $durationTitle;
                    $content['is_ai_created'] = $request->is_ai_created;
                    $content['skills'] = $updatedSkills;
                    $content['resource_modules'] = $request->resource_modules;
                    $content['resource_module_prepr'] = $request->resource_module_prepr;
                    $content['resource_module_openai'] = $request->resource_module_openai;
                    $content['resource_module_go1'] = $request->resource_module_go1;
                    $content['openai_resource_module_types'] = $request->openai_resource_module_types;
                    $content['go1_resource_module_types'] = $request->go1_resource_module_types;

                    $validChallenges[] = $content;
                }

                $endTimeValidatingChallenges = microtime(true);
                Log::info('Validating challenges duration: ' . ($endTimeValidatingChallenges - $startTimeValidatingChallenges) . ' seconds');
            }

            if (count($validChallenges) < 2 || $irrelevantDataCount > 4) {
                // Handle the scenario where valid challenges are insufficient or too many irrelevant data responses were received
                throw new Exception("Failed to generate sufficient valid challenges. Irrelevant data count: $irrelevantDataCount");
            }

            return (object)$validChallenges;
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    protected function fetchChallengesFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles)
    {
        try {
            $payload = [
                'model' => 'gpt-3.5-turbo',
                'n' => 7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $this->constructUserPromptForChallengeCreation($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles)
                    ]
                ],
            ];

            $response = $this->openAIClient->post('', ['json' => $payload]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            return null;
        }
    }

    protected function constructUserPromptForChallengeCreation($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles)
    {
        return '
            Please design an educational challenge for the careers: "' . $jobTitles . '", with skills: "' . $skillTitles . '", at level: "' . $levelTitle . '", for the duration of "' . $durationTitle . '" for the challenge to finish. Additional information would be ("' . $additionalInformation . '")
            1. **Title**: Craft a brief title for the challenge.
            2. **Description**: Provide a brief description about the challenge and a detailed, step-by-step guide in HTML format suitable for online implementation.
            3. **Steps**: Write the exact same steps mentioned in description in an array as well.
            4. **Skills**: Enumerate 10 vital skills necessary for this challenge along with the provided skills.
            5. **Category**: Based on the specified careers, skills, and level, select one category from these options: "' . $categoryTitles . '".
            6. **Reflections**: provide 3-5 reflective questions that participants can answer after completing the challenge. These questions should help participants reflect on their approach to the challenge, the skills they applied, any roadblocks they encountered, and their overall learning experience.

            Output format (Make sure you exactly follow it):
            {
            "title": "Challenge Title",
            "description": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p>...",
            "category": "Selected Category",
            "steps": ["Step 1", "Step 2", ...],
            "skills": ["Skill 1", "Skill 2", ...],
            "reflections": ["Reflection 1", "Reflection 2", ...]
            }
            
            Keep in mind that if any if given data does not make sense and are irrelevant by a large range, then return the text "Irrelevant given data!"
        ';
    }

    protected function processSkills($skills)
    {
        $updatedSkills = [];
        foreach ($skills as $skill) {
            $recommendationResponse = $this->fetchSkillRecommendation($skill);
            if ($recommendationResponse) {
                $highestScoreSkill = $this->selectHighestScoreSkill($recommendationResponse);
                if ($highestScoreSkill['score'] >= 0.92) {
                    $updatedSkills[] = $highestScoreSkill['skill'];
                }
            }
        }
        return $updatedSkills;
    }

    protected function fetchSkillRecommendation($skill)
    {
        try {
            $response = RecommendationEngineHelper::getRelatedPreprSkills(Config::get('app.skills_recommendation_engine_url') . "/" . strtolower($skill));
            return $response;
        } catch (Exception $e) {
            // Log error or handle exception
            return null;
        }
    }

    protected function selectHighestScoreSkill($recommendations)
    {
        $highestScore = 0;
        $highestScoreSkill = null;
        foreach ($recommendations as $skill => $score) {
            if ($score > $highestScore) {
                $highestScore = $score;
                $highestScoreSkill = $skill;
            }
        }
        return ['skill' => $highestScoreSkill, 'score' => $highestScore];
    }

    // Create resource modules from challenges
    public function createResourceModuleUsingAI($request)
    {
        // $language = $this->language;
        $bingArticleClient = $this->bingArticleClient;
        $bingVideoClient = $this->bingVideoClient;

        $title = $request->title;

        // $durationTitle = $request->duration;

        $levelTitle = $request->level;

        $data = ['articles' => [], 'videos' => []];

        $maxAttempts = 3;
        $minArticleCount = 6;
        $minVideoCount = 6;
        $minCombinedCount = 10;

        if ($request->resource_module_openai && $title) {
            $attempts = 0;
            $articlesCollected = false;
            $videosCollected = false;

            while ($attempts < $maxAttempts && (!$articlesCollected || !$videosCollected)) {
                $attempts++;
                $currentData = ['articles' => [], 'videos' => []];

                if (collect($request->openai_resource_module_types)->contains('links')) {
                    try {
                        $articleResponse = $bingArticleClient->request('GET', '', [
                            'query' => ['q' => 'Articles about ' . $title . ' for level ' . $levelTitle, 'count' => 15],
                        ]);
                        $articleResponse = json_decode($articleResponse->getBody(), true);

                        foreach ($articleResponse['webPages']['value'] as $item) {
                            $article = ['type' => 'link', 'title' => $item['name'] ?? '', 'description' => $item['snippet'] ?? '', 'url' => $item['url'] ?? ''];
                            if (!empty($article['title'])) {
                                $currentData['articles'][] = $article;
                            }
                        }

                        $articlesCollected = count($currentData['articles']) >= $minArticleCount;
                    } catch (\Exception $e) {
                        Log::error("Article fetch attempt $attempts failed: " . $e->getMessage());
                        // No need to explicitly do anything here, the loop will continue or end based on conditions
                    }
                }

                if (collect($request->openai_resource_module_types)->contains('videos')) {
                    try {
                        $videoResponse = $bingVideoClient->request('GET', '', [
                            'query' => ['q' => 'Videos about ' . $title . ' for level ' . $levelTitle, 'count' => 15],
                        ]);
                        $videoResponse = json_decode($videoResponse->getBody(), true);

                        foreach ($videoResponse['value'] as $video) {
                            $videoData = [
                                'type' => 'video',
                                'title' => $video['name'] ?? '',
                                'description' => $video['description'] ?? '',
                                'publisher' => $video['publisher'][0]['name'] ?? 'Unknown Publisher',
                                'url' => $video['contentUrl'] ?? '',
                                'embedHTML' => $video['embedHtml'] ?? ''
                            ];
                            if (!empty($videoData['title'])) {
                                $currentData['videos'][] = $videoData;
                            }
                        }

                        $videosCollected = count($currentData['videos']) >= $minVideoCount;
                    } catch (\Exception $e) {
                        Log::error("Video fetch attempt $attempts failed: " . $e->getMessage());
                        // No need to explicitly do anything here, the loop will continue or end based on conditions
                    }
                }

                // Check if enough data has been collected
                if ($articlesCollected && $videosCollected) {
                    if (count($currentData['articles']) + count($currentData['videos']) >= $minCombinedCount) {
                        // If enough articles and videos are collected
                        $data = $currentData;
                        break;
                    }
                } elseif ($articlesCollected || $videosCollected) {
                    // If only one type is selected and enough data is collected
                    $data = $currentData;
                    break;
                }
                // If conditions are not met, loop will continue for a retry
            }

            if (!$articlesCollected || !$videosCollected) {
                Log::error("Failed to collect sufficient articles/videos after $maxAttempts attempts.");
                // Handle failure as appropriate, e.g., return an error response or set a flag
            }

            // Proceed with $data, which now contains the collected articles and/or videos
        }

        function makeResourceGroups($data, $request)
        {
            $resourceTypes = collect($request->openai_resource_module_types ?? []);

            $articlesSelected = $resourceTypes->contains('links');
            $videosSelected = $resourceTypes->contains('videos');

            // Combine and prioritize articles and videos
            $allArticles = array_merge(
                array_filter($data['articles'], function ($article) {
                    return !empty($article['description']);
                }),
                array_filter($data['articles'], function ($article) {
                    return empty($article['description']);
                })
            );

            $allVideos = array_merge(
                array_filter($data['videos'], function ($video) {
                    return !empty($video['embedHTML']);
                }),
                array_filter($data['videos'], function ($video) {
                    return empty($video['embedHTML']);
                })
            );

            $combinedGroups = [];

            while (!empty($allArticles) || !empty($allVideos)) {
                $group = [];
                if ($articlesSelected && $videosSelected) {
                    if (count($allArticles) >= 2 && count($allVideos) >= 2) {
                        $group = array_merge(array_splice($allArticles, 0, 2), array_splice($allVideos, 0, 2));
                    }
                } elseif ($articlesSelected) {
                    if (count($allArticles) >= 3) {
                        $group = array_splice($allArticles, 0, 3);
                    }
                } elseif ($videosSelected) {
                    if (count($allVideos) >= 3) {
                        $group = array_splice($allVideos, 0, 3);
                    }
                }

                if (empty($group)) {
                    break; // Exit loop if no full group can be formed
                }

                $combinedGroups[] = $group;
            }

            return $combinedGroups;
        }

        $combinedGroups = makeResourceGroups($data, $request);

        if ($combinedGroups) {
            try {
                $chunks = array_chunk($combinedGroups, 4, true);

                $allAiResults = []; // To store results from all AI calls

                foreach ($chunks as $chunkIndex => $chunk) {
                    $chunkGroupDescriptions = [];
                    foreach ($chunk as $groupIndex => $group) {
                        $descriptionParts = [];
                        foreach ($group as $item) {
                            $title = $item['title'];
                            $description = isset($item['description']) ? $item['description'] : "No description available.";

                            $descriptionParts[] = "{$title} - {$description}";
                        }
                        $chunkGroupDescriptions[] = "Group " . ($groupIndex + 1) . ": " . implode(", ", $descriptionParts);
                    }

                    $combinedChunkDescription = implode(" ", $chunkGroupDescriptions);

                    $prompt = "For each group described below, generate a title and a super brief complete description. Format your response as a JSON object with a 'results' key containing an array of objects, each with 'title' and 'description' keys: " . $combinedChunkDescription .
                        " Example format: {\"results\": [{\"title\": \"Title 1\", \"description\": \"Description 1\"}, {\"title\": \"Title 2\", \"description\": \"Description 2\"}]}";

                    $payload = [
                        'model' => 'gpt-3.5-turbo',
                        'n' => 1,
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ],
                    ];

                    $response = $this->openAIClient->post('', ['json' => $payload]);
                    $responseBody = $response->getBody()->getContents();
                    $responseArray = json_decode($responseBody, true);

                    if (isset($responseArray['choices'][0]['message']['content'])) {
                        $contentString = $responseArray['choices'][0]['message']['content'];
                        $contentArray = json_decode($contentString, true);

                        if (isset($contentArray['results'])) {
                            // Append AI results for this chunk to the overall results
                            $allAiResults = array_merge($allAiResults, $contentArray['results']);
                        } else {
                            Log::error('The parsed AI response did not contain the expected "results" key for chunk ' . $chunkIndex);
                        }
                    } else {
                        Log::error('The AI response structure is not as expected for chunk ' . $chunkIndex);
                    }
                }

                // Update the original groups with the titles and descriptions from AI
                foreach ($combinedGroups as $index => &$group) {
                    if (isset($allAiResults[$index])) {
                        $group['title'] = $allAiResults[$index]['title'];
                        $group['description'] = $allAiResults[$index]['description'];
                    } else {
                        $group['title'] = 'No title generated';
                        $group['description'] = 'No description generated';
                    }
                }
                unset($group); // Break the reference with the last element
            } catch (Exception $e) {
                Log::error($e);
            }
        }

        return $combinedGroups;
    }
}
