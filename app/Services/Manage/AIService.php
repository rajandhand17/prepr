<?php

namespace App\Services\Manage;

use App\Helpers\RecommendationEngineHelper;
use App\Helpers\UtilityHelper;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Duration;
use App\Models\JobTitle;
use App\Models\Levels;
use App\Models\ResourceModule;
use App\Models\Skill;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $openAIClient;
    protected $bingArticleClient;
    protected $bingVideoClient;

    public function __construct()
    {
        $openAIAPIKey = config('ai.openai_api_key');
        $bingAPIKey = config('ai.bing_api_key');

        $this->openAIClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/chat/completions',
            'headers'  => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $openAIAPIKey,
            ],
        ]);

        $this->bingArticleClient = new Client([
            'base_uri' => 'https://api.bing.microsoft.com/v7.0/search',
            'headers'  => [
                'Content-Type'              => 'application/json',
                'Ocp-Apim-Subscription-Key' => $bingAPIKey,
            ],
        ]);

        $this->bingVideoClient = new Client([
            'base_uri' => 'https://api.bing.microsoft.com/v7.0/videos/search',
            'headers'  => [
                'Content-Type'              => 'application/json',
                'Ocp-Apim-Subscription-Key' => $bingAPIKey,
            ],
        ]);
    }

    public function createChallengeUsingAIPreview($request)
    {
        try {
            $attempt = 0;
            $validChallenges = [];

            // $language = $request->language;

            $jobTitlesArray = UtilityHelper::objectToArray(JobTitle::whereIn('id', $request->jobs)->pluck('title'));
            $jobTitles = implode(', ', $jobTitlesArray);
            $skillTitlesArray = UtilityHelper::objectToArray(Skill::whereIn('id', $request->skills)->get()->pluck('title'));
            $skillTitles = implode(', ', $skillTitlesArray);
            $durationTitle = Duration::find($request->duration_id)->title;
            $levelTitle = Levels::find($request->level_id)->title;
            $additionalInformation = $request->additional_information;
            $categoryTitles = Category::pluck('title')->implode(', ');

            while ($attempt < 3 && count($validChallenges) < 2) {
                // Log::info('Attempt: ' . $attempt + 1);
                $attempt++;

                $startTimeAPI = microtime(true);
                $openAIResponse = $this->fetchChallengesFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles);
                $endTimeAPI = microtime(true);
                // Log::info('API call duration: ' . ($endTimeAPI - $startTimeAPI) . ' seconds');

                if (!$openAIResponse || empty($openAIResponse['choices'])) {
                    continue;
                }

                // $startTimeValidatingChallenges = microtime(true);

                foreach ($openAIResponse['choices'] as $choice) {
                    $content = json_decode($choice['message']['content'], true);

                    // Checks for duplicate names in all challenges so no duplicate titles would exist
                    if (is_array($content) && isset($content['title'])) {
                        if (Challenge::where('title', $content['title'])->exists()) {
                            continue;
                        }
                    }

                    if (empty($content['skills'])) {
                        continue;
                    }

                    // $startTimeValidatingSkills = microtime(true);
                    $updatedSkills = $this->processSkills($content['skills']);

                    // Removing the duplicates and reindexing
                    $updatedSkills = array_unique($updatedSkills);
                    $updatedSkills = array_values($updatedSkills);
                    // $endTimeValidatingSkills = microtime(true);
                    // Log::info('Validating skills duration: ' . ($endTimeValidatingSkills - $startTimeValidatingSkills) . ' seconds');

                    // Making sure each challenge has more than 6 verified skill
                    if (count($updatedSkills) < 6 || !isset($content['title'])) {
                        continue;
                    }

                    $skillIds = UtilityHelper::objectToArray(Skill::whereIn('title', $updatedSkills)->get(['id'])->pluck('id'));

                    $content['level'] = $levelTitle;
                    $content['level_id'] = Levels::where('title', $content['level'])->pluck('id')->first();
                    $content['duration'] = $durationTitle;
                    $content['duration_id'] = Duration::where('title', $content['duration'])->pluck('id')->first();
                    $content['is_ai_created'] = $request->is_ai_created;
                    $content['skill_titles'] = $updatedSkills;
                    $content['skills'] = $skillIds;
                    $content['job_titles'] = $jobTitlesArray;
                    $content['jobs'] = $request->jobs;
                    $content['resource_modules'] = $request->resource_modules;
                    $content['resource_module_prepr'] = $request->resource_module_prepr;
                    $content['resource_module_openai'] = $request->resource_module_openai;
                    $content['resource_module_go1'] = $request->resource_module_go1;
                    $content['openai_resource_module_types'] = $request->openai_resource_module_types;
                    $content['go1_resource_module_types'] = $request->go1_resource_module_types;
                    $content['category_id'] = Category::where('title', $content['category'])->pluck('id')->first();

                    $validChallenges[] = $content;
                }

                // $endTimeValidatingChallenges = microtime(true);
                // Log::info('Validating challenges duration: ' . ($endTimeValidatingChallenges - $startTimeValidatingChallenges) . ' seconds');
            }

            if (count($validChallenges) < 2) {
                throw new Exception('Failed to generate sufficient valid challenges.');
            }

            return (object) $validChallenges;
        } catch (Exception $e) {
            Log::error('Error in createChallengeUsingAIPreview in AIService.php: ' . $e->getMessage());

            return false;
        }
    }

    public function createChallengesForLabUsingAIPreview($request)
    {
        try {
            $attempt = 0;
            $validLabs = [];

            // $language = $request->language;

            $jobTitlesArray = UtilityHelper::objectToArray(JobTitle::whereIn('id', $request->jobs)->pluck('title'));
            $jobTitles = implode(', ', $jobTitlesArray);
            $skillTitlesArray = UtilityHelper::objectToArray(Skill::whereIn('id', $request->skills)->get()->pluck('title'));
            $skillTitles = implode(', ', $skillTitlesArray);
            $durationTitle = Duration::find($request->duration_id)->title;
            $levelTitle = Levels::find($request->level_id)->title;
            $additionalInformation = $request->additional_information;
            $categoryTitles = Category::pluck('title')->implode(', ');

            while ($attempt < 3 && count($validLabs) < 2) {
                $attempt++;

                $openAIResponse = $this->fetchChallengesForLabFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles);

                if (!$openAIResponse || empty($openAIResponse['choices'])) {
                    continue;
                }

                foreach ($openAIResponse['choices'] as $choice) {
                    $lab = json_decode($choice['message']['content'], true);
                    if (isset($lab['challenges']) && is_array($lab['challenges'])) {
                        $allChallengesValid = true;
                        foreach ($lab['challenges'] as $challenge) {
                            if (isset($challenge['title']) && Challenge::where('title', $challenge['title'])->exists()) {
                                $allChallengesValid = false;
                                break;
                            }

                            if (empty($challenge['skills']) || count($challenge['skills']) < 4) {
                                $allChallengesValid = false;
                                break;
                            }

                            $updatedSkills = $this->processSkills($challenge['skills']);
                            $updatedSkills = array_unique($updatedSkills);
                            $updatedSkills = array_values($updatedSkills);

                            $skillIds = UtilityHelper::objectToArray(Skill::whereIn('title', $updatedSkills)->pluck('id'));

                            // Append processed data to challenge
                            $challenge += [
                                'level' => $levelTitle,
                                'level_id' => Levels::where('title', $levelTitle)->pluck('id')->first(),
                                'duration' => $durationTitle,
                                'duration_id' => Duration::where('title', $durationTitle)->pluck('id')->first(),
                                'is_ai_created' => $request->is_ai_created,
                                'skill_titles' => $updatedSkills,
                                'skills' => $skillIds,
                                'job_titles' => $jobTitlesArray,
                                'jobs' => $request->jobs,
                                'resource_modules' => $request->resource_modules,
                                'resource_module_prepr' => $request->resource_module_prepr,
                                'resource_module_openai' => $request->resource_module_openai,
                                'resource_module_go1' => $request->resource_module_go1,
                                'openai_resource_module_types' => $request->openai_resource_module_types,
                                'go1_resource_module_types' => $request->go1_resource_module_types,
                                'category_id' => Category::where('title', $challenge['category'])->pluck('id')->first()
                            ];

                            $processedChallenges[] = $challenge;
                        }

                        if ($allChallengesValid && !empty($processedChallenges)) {
                            $validLabs[] = [
                                'labTitle' => $lab['labTitle'],
                                'labDesc' => $lab['labDesc'],
                                'challenges' => $processedChallenges
                            ];
                        }
                    }
                }
            }

            if (count($validLabs) < 2) {
                throw new Exception('Failed to generate sufficient valid labs.');
            }

            return (object) $validLabs;
        } catch (Exception $e) {
            Log::error('Error in createChallengesForLabUsingAIPreview: ' . $e->getMessage());
            return false;
        }
    }

    protected function fetchChallengesFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles)
    {
        try {
            $payload = [
                'model'    => 'gpt-3.5-turbo',
                'n'        => 10,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => '
                            Please design an educational challenge for the careers: "' . $jobTitles . '", with skills: "' . $skillTitles . '", at level: "' . $levelTitle . '", for the duration of "' . $durationTitle . '" for the challenge to finish. Additional information that needs to be prioritize would be ("' . $additionalInformation . '").
                            1. **Title**: Craft a brief title for the challenge.
                            2. **Description**: Provide a paragraph description about the challenge and a detailed, step-by-step guide in HTML format suitable for online implementation.
                            3. **Steps**: Write the exact same steps mentioned in description in an array as well.
                            4. **Skills**: Enumerate 10 vital skills necessary for this challenge. Make sure the provided skills are among them as well.
                            5. **Category**: Based on the specified careers, skills, and level, select one category from these options: "' . $categoryTitles . '".
                            6. **Reflections**: provide 5 reflective questions that participants can answer after completing the challenge. These questions should help participants reflect on their approach to the challenge, the skills they applied, any roadblocks they encountered, and their overall learning experience.
                
                            Output format (Make sure you exactly follow it):
                            {
                            "title": "Challenge Title",
                            "description": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p> (and so on)",
                            "category": "Selected Category",
                            "steps": ["Step 1", "Step 2", (and so on)],
                            "skills": ["Skill 1", "Skill 2", (and so on)],
                            "reflections": ["Reflection 1", "Reflection 2", (and so on)]
                        }',
                    ],
                ],
            ];

            try {
                $response = $this->openAIClient->post('', ['json' => $payload]);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error('Error in fetchChallengesFromOpenAI in AIService.php: ' . $e->getMessage());

            return false;
        }
    }

    protected function fetchChallengesForLabFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles)
    {
        try {
            $payload = [
                'model'    => 'gpt-3.5-turbo',
                'n'        => 10,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => '
                            Please design an educational lab with 4 challenges for the careers: "' . $jobTitles . '", with skills: "' . $skillTitles . '", at level: "' . $levelTitle . '", for the duration of "' . $durationTitle . '" for the lab to finish. Additional information that needs to be prioritize would be ("' . $additionalInformation . '"). The challenges must be in order and preferably follow each other to reach the lab\'s goal.
                            1. **Title**: Craft a brief title for the challenge.
                            2. **Description**: Provide a paragraph description about the challenge and a detailed, step-by-step guide in HTML format suitable for online implementation.
                            3. **Steps**: Write the exact same steps mentioned in description in an array as well.
                            4. **Skills**: Enumerate 10 vital skills necessary for this challenge. Make sure the provided skills are among them as well.
                            5. **Category**: Based on the specified careers, skills, and level, select one category from these options: "' . $categoryTitles . '".
                            6. **Reflections**: provide 5 reflective questions that participants can answer after completing the challenge. These questions should help participants reflect on their approach to the challenge, the skills they applied, any roadblocks they encountered, and their overall learning experience.
                            6. **Lab Title**: Craft a brief title for the lab.
                            6. **Lab Description**: Provide a paragraph description about the lab and what it focuses on.
                
                            Output format (Make sure you exactly follow it):
                                {
                                    "labTitle": "Lab Title",
                                    "labDesc": "Lab Description"
                                    "challenges": [
                                        {
                                            "title": "Challenge Title",
                                            "description": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p> (and so on)",
                                            "category": "Selected Category",
                                            "steps": ["Step 1", "Step 2", (and so on)],
                                            "skills": ["Skill 1", "Skill 2", (and so on)],
                                            "reflections": ["Reflection 1", "Reflection 2", (and so on)]
                                        },
                                        ...
                                        (4 challenges)
                                    ]
                                }
                            ',
                    ],
                ],
            ];

            try {
                $response = $this->openAIClient->post('', ['json' => $payload]);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error('Error in fetchChallengesFromOpenAI in AIService.php: ' . $e->getMessage());

            return false;
        }
    }

    protected function processSkills($skills)
    {
        $updatedSkills = [];
        $lowercaseSkills = array_map('strtolower', $skills);

        try {
            $recommendationResponse = RecommendationEngineHelper::getRelatedPreprSkills($lowercaseSkills);
            foreach ($recommendationResponse as $skill) {
                if (is_array($skill) && $skill) {
                    $highestScoreSkill = $this->selectHighestScoreSkill($skill);
                    if ($highestScoreSkill['score'] >= 0.92) {
                        $updatedSkills[] = $highestScoreSkill['skill'];
                    }
                }
            }

            return $updatedSkills;
        } catch (Exception $e) {
            Log::error('Error in processSkills in AIService.php: ' . $e->getMessage());

            return false;
        }
    }

    protected function selectHighestScoreSkill($recommendations)
    {
        $highestScore = 0;
        $highestScoreSkill = null;

        try {
            foreach ($recommendations as $skill => $score) {
                if ($score > $highestScore) {
                    $highestScore = $score;
                    $highestScoreSkill = $skill;
                }
            }

            return ['skill' => $highestScoreSkill, 'score' => $highestScore];
        } catch (Exception $e) {
            Log::error('Error in selectHighestScoreSkill in AIService.php: ' . $e->getMessage());

            return false;
        }
    }

    // Create resource modules from challenges
    public function createResourceModuleUsingAIPreview($request)
    {
        // $language = $this->language;
        // $durationTitle = $request->duration;
        $title = $request->title;
        $levelTitle = $request->level;

        $data = ['articles' => [], 'videos' => []];

        $maxAttempts = 3;

        if ($request->resource_module_openai && $title) {
            $attempts = 0;
            $articlesCollected = false;
            $videosCollected = false;
            $collectArticles = collect($request->openai_resource_module_types)->contains('links');
            $collectVideos = collect($request->openai_resource_module_types)->contains('videos');

            try {
                while ($attempts < $maxAttempts && ($collectArticles ? !$articlesCollected : true) && ($collectVideos ? !$videosCollected : true)) {
                    $attempts++;
                    $currentData = ['articles' => [], 'videos' => []];

                    if ($collectArticles && !$articlesCollected) {
                        try {
                            $articleResponse = $this->bingArticleClient->request('GET', '', [
                                'query' => ['q' => 'Articles about ' . $title . ' for level ' . $levelTitle, 'count' => 15],
                            ]);
                            $articleResponse = json_decode($articleResponse->getBody(), true);

                            foreach ($articleResponse['webPages']['value'] as $item) {
                                $article = [
                                    'type'        => 'link',
                                    'title'       => $item['name'],
                                    'description' => $item['snippet'] ?? '',
                                    'url'         => $item['url'],
                                ];
                                if (!empty($article['title'])) {
                                    $currentData['articles'][] = $article;
                                }
                            }

                            $articlesCollected = count($currentData['articles']) >= 6;
                        } catch (Exception $e) {
                            throw new Exception($e->getMessage());
                        }
                    }

                    if ($collectVideos && !$videosCollected) {
                        try {
                            $videoResponse = $this->bingVideoClient->request('GET', '', [
                                'query' => ['q' => 'Videos about ' . $title . ' for level ' . $levelTitle, 'count' => 15],
                            ]);
                            $videoResponse = json_decode($videoResponse->getBody(), true);

                            foreach ($videoResponse['value'] as $video) {
                                $videoData = [
                                    'type'        => 'video',
                                    'title'       => $video['name'],
                                    'description' => $video['description'] ?? '',
                                    'publisher'   => $video['publisher'][0]['name'] ?? '',
                                    'url'         => $video['contentUrl'],
                                    'embedHTML'   => $video['embedHtml'] ?? '',
                                ];
                                if (!empty($videoData['title'])) {
                                    $currentData['videos'][] = $videoData;
                                }
                            }

                            $videosCollected = count($currentData['videos']) >= 6;
                        } catch (Exception $e) {
                            throw new Exception($e->getMessage());
                        }
                    }

                    if (($collectArticles && $collectVideos) && ($articlesCollected && $videosCollected)) {
                        if (count($currentData['articles']) + count($currentData['videos']) >= 10) {
                            $data = $currentData;
                            break;
                        }
                    } elseif (($collectArticles && $articlesCollected) || ($collectVideos && $videosCollected)) {
                        $data = $currentData;
                        break;
                    }
                }

                if (($collectArticles ? $articlesCollected : true) && ($collectVideos ? $videosCollected : true)) {
                    throw new Exception('Error in gathering enough data!');
                }
            } catch (Exception $e) {
                Log::error("Error in createResourceModuleUsingAIPreview in attempt $attempts in AIService.php: " . $e->getMessage());
            }
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

                $combinedGroups[] = ['resource_modules' => $group];
            }

            return $combinedGroups;
        }

        $combinedGroups = makeResourceGroups($data, $request);

        if ($combinedGroups) {
            try {
                $chunks = array_chunk($combinedGroups, 4, true);

                $allAiResults = [];

                foreach ($chunks as $chunkIndex => $chunk) {
                    $chunkGroupDescriptions = [];
                    foreach ($chunk as $groupIndex => $group) {
                        $descriptionParts = [];
                        foreach ($group['resource_modules'] as $item) {
                            $title = $item['title'];
                            $description = isset($item['description']) ? $item['description'] : 'No description available.';

                            $descriptionParts[] = "{$title} - {$description}";
                        }
                        $chunkGroupDescriptions[] = 'Group ' . ($groupIndex + 1) . ': ' . implode(', ', $descriptionParts);
                    }

                    $combinedChunkDescription = implode(' ', $chunkGroupDescriptions);

                    $prompt = "For each group described below, generate a title and a super brief complete description. Format your response as a JSON object with a 'results' key containing an array of objects, each with 'title' and 'description' keys: " . $combinedChunkDescription .
                        ' Example format: {"results": [{"title": "Title 1", "description": "Description 1"}, {"title": "Title 2", "description": "Description 2"}]}';

                    $payload = [
                        'model'    => 'gpt-3.5-turbo',
                        'n'        => 1,
                        'messages' => [
                            [
                                'role'    => 'user',
                                'content' => $prompt,
                            ],
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
                        $content = $allAiResults[$index];

                        $newResourceModule = [];

                        if (is_array($content) && isset($content['title'])) {
                            // Convert the title to lowercase and check if it already exists in ResourceModule
                            if (ResourceModule::where('title', $content['title'])->exists()) {
                                // If the title already exists, set title and description to 'Resource Module'
                                $newResourceModule['title'] = 'Resource Module';
                                $newResourceModule['description'] = 'Resource Module';
                            } else {
                                // If the title does not exist, use the title and description from $allAiResults[$index]
                                $newResourceModule['title'] = $content['title'];
                                $newResourceModule['description'] = $content['description'];
                            }
                        } else {
                            // If $content is not an array or does not have a title, use default 'Resource Module'
                            $newResourceModule['title'] = 'Resource Module';
                            $newResourceModule['description'] = 'Resource Module';
                        }

                        // Append the new resource module to the 'resource_modules' array of the group
                        $group['title'] = $newResourceModule['title'];
                        $group['description'] = $newResourceModule['description'];
                    }

                    $group['skill_titles'] = $request->skill_titles;
                    $group['skills'] = $request->skills;
                    $group['level'] = Levels::find($request->level_id)->title;
                    $group['level_id'] = Levels::where('title', $group['level'])->pluck('id')->first();
                    $group['duration'] = Duration::find($request->duration_id)->title;
                    $group['duration_id'] = Duration::where('title', $group['duration'])->pluck('id')->first();
                    $group['is_ai_created'] = $request->is_ai_created;
                }
                unset($group); // Unset the reference to the last element
            } catch (Exception $e) {
                Log::error('Error in createResourceModuleUsingAIPreview in AIService.php: ' . $e->getMessage());
            }
        }

        return $combinedGroups;
    }
}
