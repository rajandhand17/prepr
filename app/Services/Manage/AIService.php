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
                'Authorization' => 'Bearer '.$openAIAPIKey,
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

            $jobIdsArray = array_map(function ($job) {
                return $job['key'];
            }, $request['jobs']);
            $jobTitlesArray = array_map(function ($job) {
                return $job['value'];
            }, $request['jobs']);
            $jobTitles = implode(', ', $jobTitlesArray);

            $skillTitlesArray = array_map(function ($skill) {
                return $skill['value'];
            }, $request['skills']);
            $skillTitles = implode(', ', $skillTitlesArray);

            $durationTitlesArray = array_map(function ($duration) {
                return $duration['value'];
            }, $request['duration_id']);
            $durationTitle = implode($durationTitlesArray);

            $levelTitlesArray = array_map(function ($level) {
                return $level['value'];
            }, $request['level_id']);
            $levelTitles = implode($levelTitlesArray);

            $additionalInformation = $request['additional_information'] ?? 'No additional information';

            $categoryTitles = Category::pluck('title')->implode(', ');

            while ($attempt < 3 && count($validChallenges) < 2) {
                $attempt++;

                $openAIResponse = $this->fetchChallengesFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitles, $additionalInformation, $categoryTitles);

                if (!$openAIResponse || empty($openAIResponse['choices'])) {
                    continue;
                }

                foreach ($openAIResponse['choices'] as $choice) {
                    $content = json_decode($choice['message']['content'], true);

                    // Checks for duplicate names in all challenges so no duplicate titles would exist
                    if (is_array($content) && isset($content['challengeTitle'])) {
                        if (Challenge::where('title', $content['challengeTitle'])->exists()) {
                            continue;
                        }
                    }

                    if (empty($content['skills'])) {
                        continue;
                    }

                    $updatedSkills = $this->processSkills($content['skills']);
                    $updatedSkills = array_values($updatedSkills);
                    $mergedSkills = array_merge($skillTitlesArray, $updatedSkills);
                    // Making sure each challenge has more than 5 verified skill
                    if (count($mergedSkills) < 5 || !isset($content['challengeTitle'])) {
                        continue;
                    }

                    $skills = Skill::whereIn('title', $mergedSkills)->get(['id', 'title']);
                    $skillIds = $skills->pluck('id')->toArray();
                    $skillTitles = array_unique($skills->pluck('title')->toArray());

                    $content['level'] = $levelTitles;
                    $content['level_id'] = Levels::where('title', $content['level'])->pluck('id')->first();
                    $content['duration'] = $durationTitle;
                    $content['duration_id'] = Duration::where('title', $content['duration'])->pluck('id')->first();
                    $content['is_ai_created'] = $request->is_ai_created;
                    $content['skill_titles'] = $skillTitles;
                    $content['skills'] = $skillIds;
                    $content['job_titles'] = $jobTitlesArray;
                    $content['jobs'] = $jobIdsArray;
                    $content['resource_modules'] = $request->resource_modules;
                    $content['resource_module_prepr'] = $request->resource_module_prepr;
                    $content['resource_module_openai'] = $request->resource_module_openai;
                    $content['resource_module_go1'] = $request->resource_module_go1;
                    $content['openai_resource_module_types'] = $request->openai_resource_module_types;
                    $content['go1_resource_module_types'] = $request->go1_resource_module_types;
                    $content['category_id'] = Category::where('title', $content['category'])->pluck('id')->first();

                    $validChallenges[] = $content;
                }
            }

            if (count($validChallenges) < 2) {
                throw new Exception('Failed to generate sufficient valid challenges.');
            }

            return $validChallenges;
        } catch (Exception $e) {
            Log::error('Error in createChallengeUsingAIPreview in AIService.php: '.$e->getMessage());

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
            $levelTitles = Levels::find($request->level_id)->title;
            $additionalInformation = $request->additional_information;
            $categoryTitles = Category::pluck('title')->implode(', ');

            while ($attempt < 3 && count($validLabs) < 2) {
                $attempt++;

                $openAIResponse = $this->fetchChallengesForLabFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitles, $additionalInformation, $categoryTitles);

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
                                'level'                        => $levelTitles,
                                'level_id'                     => Levels::where('title', $levelTitles)->pluck('id')->first(),
                                'duration'                     => $durationTitle,
                                'duration_id'                  => Duration::where('title', $durationTitle)->pluck('id')->first(),
                                'is_ai_created'                => $request->is_ai_created,
                                'skill_titles'                 => $updatedSkills,
                                'skills'                       => $skillIds,
                                'job_titles'                   => $jobTitlesArray,
                                'jobs'                         => $request->jobs,
                                'resource_modules'             => $request->resource_modules,
                                'resource_module_prepr'        => $request->resource_module_prepr,
                                'resource_module_openai'       => $request->resource_module_openai,
                                'resource_module_go1'          => $request->resource_module_go1,
                                'openai_resource_module_types' => $request->openai_resource_module_types,
                                'go1_resource_module_types'    => $request->go1_resource_module_types,
                                'category_id'                  => Category::where('title', $challenge['category'])->pluck('id')->first(),
                            ];

                            $processedChallenges[] = $challenge;
                        }

                        if ($allChallengesValid && !empty($processedChallenges)) {
                            $validLabs[] = [
                                'labTitle'          => $lab['labTitle'],
                                'labDescription'    => $lab['labDescription'],
                                'challenges'        => $processedChallenges,
                            ];
                        }
                    }
                }
            }

            if (count($validLabs) < 2) {
                throw new Exception('Failed to generate sufficient valid labs.');
            }

            return $validLabs;
        } catch (Exception $e) {
            Log::error('Error in createChallengesForLabUsingAIPreview: '.$e->getMessage());

            return false;
        }
    }

    protected function fetchChallengesFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitles, $additionalInformation, $categoryTitles)
    {
        try {
            $payload = [
                'model'    => 'gpt-3.5-turbo',
                'n'        => 10,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => '
                            Please design an educational challenge for the careers: "'.$jobTitles.'", with skills: "'.$skillTitles.'", at level: "'.$levelTitles.'", for the duration of "'.$durationTitle.'" for the challenge to finish. Additional information that needs to be prioritize would be ("'.$additionalInformation.'").
                            1. **Title**: Craft a brief title for the challenge.
                            2. **Description**: Provide a paragraph description about the challenge and a detailed, step-by-step guide in HTML format suitable for online implementation.
                            3. **Steps**: Write the exact same steps mentioned in description in an array as well.
                            4. **Skills**: Enumerate 10 vital skills necessary for this challenge. Make sure the provided skills are among them as well. Add the important skills first.
                            5. **Category**: Based on the specified careers, skills, and level, select one category from these options: "'.$categoryTitles.'".
                            6. **Reflections**: provide 5 reflective questions that participants can answer after completing the challenge. These questions should help participants reflect on their approach to the challenge, the skills they applied, any roadblocks they encountered, and their overall learning experience.
                
                            Output format (Make sure you exactly follow it):
                            {
                            "challengeTitle": "Challenge Title",
                            "challengeDescription": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p> (and so on)",
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
            Log::error('Error in fetchChallengesFromOpenAI in AIService.php: '.$e->getMessage());

            return false;
        }
    }

    protected function fetchChallengesForLabFromOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitles, $additionalInformation, $categoryTitles)
    {
        try {
            $payload = [
                'model'    => 'gpt-3.5-turbo',
                'n'        => 10,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => '
                            Please design an educational lab with 4 challenges for the careers: "'.$jobTitles.'", with skills: "'.$skillTitles.'", at level: "'.$levelTitles.'", for the duration of "'.$durationTitle.'" for the lab to finish. Additional information that needs to be prioritize would be ("'.$additionalInformation.'"). The challenges must be in order and preferably follow each other to reach the lab\'s goal.
                            1. **Title**: Craft a brief title for the challenge.
                            2. **Description**: Provide a paragraph description about the challenge and a detailed, step-by-step guide in HTML format suitable for online implementation.
                            3. **Steps**: Write the exact same steps mentioned in description in an array as well.
                            4. **Skills**: Enumerate 10 vital skills necessary for this challenge. Make sure the provided skills are among them as well. Add the important skills first.
                            5. **Category**: Based on the specified careers, skills, and level, select one category from these options: "'.$categoryTitles.'".
                            6. **Reflections**: provide 5 reflective questions that participants can answer after completing the challenge. These questions should help participants reflect on their approach to the challenge, the skills they applied, any roadblocks they encountered, and their overall learning experience.
                            6. **Lab Title**: Craft a brief title for the lab.
                            6. **Lab Description**: Provide a paragraph description about the lab and what it focuses on.
                
                            Output format (Make sure you exactly follow it):
                                {
                                    "labTitle": "Lab Title",
                                    "labDescription": "Lab Description"
                                    "challenges": [
                                        {
                                            "challengeTitle": "Challenge Title",
                                            "challengeDescription": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p> (and so on)",
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
            Log::error('Error in fetchChallengesFromOpenAI in AIService.php: '.$e->getMessage());

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
                if (is_array($skill)) {
                    $highestScoreSkill = $this->selectHighestScoreSkill($skill);
                    if ($highestScoreSkill['score'] >= 0.92) {
                        $updatedSkills[] = $highestScoreSkill['skill'];
                    }
                }
            }

            return $updatedSkills;
        } catch (Exception $e) {
            Log::error('Error in processSkills in AIService.php: '.$e->getMessage());

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
            Log::error('Error in selectHighestScoreSkill in AIService.php: '.$e->getMessage());

            return false;
        }
    }

    public function createResourceModuleUsingAIPreview($request)
    {
        $language = $request->language;

        $skillIDsArray = $request->skills;

        $durationID = $request->duration_id;

        $levelID = $request->level_id;

        $levelTitle = $request->level;

        $challengeTitle = $request->challengeTitle;

        if ($request->resource_module_openai && $challengeTitle) {
            $data = ['articles' => [], 'videos' => []];

            $maxAttempts = 3;

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
                                'query' => ['q' => 'Articles about '.$challengeTitle.' for level '.$levelTitle, 'count' => 15],
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
                                'query' => ['q' => 'Videos about '.$challengeTitle.' for level '.$levelTitle, 'count' => 15],
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
                Log::warning("Error in createResourceModuleUsingAIPreview in attempt $attempts in AIService.php: ".$e->getMessage());
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

                $aiCombinedGroups = [];

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

                    $aiCombinedGroups[] = ['resource_module_items' => $group];
                }

                return $aiCombinedGroups;
            }

            $aiCombinedGroups = makeResourceGroups($data, $request);

            if ($aiCombinedGroups) {
                try {
                    $chunks = array_chunk($aiCombinedGroups, 4, true);

                    $allAiResults = [];

                    foreach ($chunks as $chunkIndex => $chunk) {
                        $chunkGroupDescriptions = [];
                        foreach ($chunk as $groupIndex => $group) {
                            $descriptionParts = [];
                            foreach ($group['resource_module_items'] as $item) {
                                $rmTitle = $item['title'];
                                $rmDescription = isset($item['description']) ? $item['description'] : 'No description available.';

                                $descriptionParts[] = "{$rmTitle} - {$rmDescription}";
                            }
                            $chunkGroupDescriptions[] = 'Group '.($groupIndex + 1).': '.implode(', ', $descriptionParts);
                        }

                        $combinedChunkDescription = implode(' ', $chunkGroupDescriptions);

                        $prompt = "For each group described below, generate a title and a super brief complete description. Format your response as a JSON object with a 'results' key containing an array of objects, each with 'title' and 'description' keys: ".$combinedChunkDescription.
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
                                $allAiResults = array_merge($allAiResults, $contentArray['results']);
                            } else {
                                Log::error('The parsed AI response did not contain the expected "results" key for chunk '.$chunkIndex);
                            }
                        } else {
                            Log::error('The AI response structure is not as expected for chunk '.$chunkIndex);
                        }
                    }

                    // Update the original groups with the titles and descriptions from AI
                    foreach ($aiCombinedGroups as $index => &$group) {
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

                            $group['title'] = $newResourceModule['title'];
                            $group['description'] = $newResourceModule['description'];
                        }

                        $group['skill_titles'] = $request->skill_titles;
                        $group['skills'] = $request->skills;
                        $group['level'] = Levels::find($levelID)->title;
                        $group['level_id'] = Levels::where('title', $group['level'])->pluck('id')->first();
                        $group['duration'] = Duration::find($durationID)->title;
                        $group['duration_id'] = Duration::where('title', $group['duration'])->pluck('id')->first();
                        $group['is_ai_created'] = $request->is_ai_created;
                    }
                    unset($group); // Unset the reference to the last element
                } catch (Exception $e) {
                    Log::error('Error in createResourceModuleUsingAIPreview in AIService.php: '.$e->getMessage());
                }
            }
        }

        if ($request->resource_module_prepr) {
            Log::info('Starting to fetch resource modules.', [
                'criteria' => [
                    'language'        => $language,
                    'level_id'        => $levelID,
                    'skills_required' => $skillIDsArray,
                    'duration_id'     => $durationID,
                ],
            ]);

            $foundModules = collect();
            $skillSubset = $skillIDsArray;

            while ($foundModules->isEmpty() && !empty($skillSubset)) {
                try {
                    // Attempt to fetch modules with current skill subset
                    $foundModules = ResourceModule::whereNull('deleted_at')
                        ->where('is_global', 1)
                        ->where('language', $language)
                        ->where('level_id', $levelID)
                        ->whereHas('skills', function ($query) use ($skillSubset) {
                            $query->whereIn('foreign_id', $skillSubset)
                                ->where('type', '0'); // Assuming type '0' is for skills
                        }, '>=', count($skillSubset) >= 3 ? 3 : 1) // Require at least 3 or just 1 matching skill, depending on subset size
                        ->with(['skills' => function ($query) {
                            $query->where('type', '0');
                        }])
                        ->when($durationID, function ($query) use ($durationID) {
                            // If duration_id is provided, prioritize matching modules
                            return $query->orderByRaw("FIELD(duration_id, {$durationID}) DESC");
                        })
                        ->get();

                    if ($foundModules->isEmpty() && count($skillSubset) > 1) {
                        // Reduce the skill subset by removing the last skill
                        array_pop($skillSubset);
                        Log::info('Reducing skill requirements.', ['new_skill_subset' => $skillSubset]);
                    } else {
                        break; // Modules found or only one skill left
                    }
                } catch (Exception $e) {
                    Log::error('Error in fetchResourceModules: '.$e->getMessage());

                    return;
                }
            }

            if ($foundModules->isEmpty()) {
                Log::warning('No modules found even after reducing skill requirements.');
            } else {
                Log::info('Fetched resource modules successfully.', ['count' => $foundModules->count()]);
            }

            // Assuming $foundModules contains the modules fetched successfully with the skill subset [1,5]
            foreach ($foundModules as $module) {
                // Assuming the module has a relationship 'skills' that can fetch its skills
                $moduleSkills = $module->skills->where('type', '0')->pluck('foreign_id')->toArray();

                // Log the details of the module, including its title and the skills it actually has
                Log::info('Detailed module information:', [
                    'title'          => $module->title,
                    'module_id'      => $module->id, // Assuming the module has an identifiable attribute like 'id'
                    'actual_skills'  => $moduleSkills,
                    'matched_skills' => array_intersect($moduleSkills, [1, 5]), // Intersection of actual skills with the final skill subset used
                ]);
            }
        }

        // Log::info($aiCombinedGroups);

        return $aiCombinedGroups;
    }
}
