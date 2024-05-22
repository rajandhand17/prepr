<?php

namespace App\Services\Manage;

use App\Helpers\GO1Helper;
use App\Helpers\RecommendationEngineHelper;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Duration;
// use App\Models\Lab;
use App\Models\Levels;
use App\Models\ProjectFile;
use App\Models\ResourceModule;
use App\Models\ResourceModuleDetail;
use App\Models\Skill;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIService
{
    protected $openAIClient;
    protected $bingArticleClient;
    protected $bingVideoClient;
    protected $resourceSummarizerClient;
    protected $projectAssessorClient;

    public function __construct()
    {
        $openAIAPIKey = config('ai.openai_api_key');
        $bingAPIKey = config('ai.bing_api_key');
        $resourceSummarizerApiKey = config('ai.resource_summarizer_api_key');
        $projectAssessorApiKey = config('ai.project_assessor_api_key');

        $this->openAIClient = new Client([
            'base_uri' => config('ai.openai_endpoint'),
            'headers'  => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$openAIAPIKey,
            ],
        ]);

        $this->bingArticleClient = new Client([
            'base_uri' => config('ai.bing_default_endpoint'),
            'headers'  => [
                'Content-Type'              => 'application/json',
                'Ocp-Apim-Subscription-Key' => $bingAPIKey,
            ],
        ]);

        $this->bingVideoClient = new Client([
            'base_uri' => config('ai.bing_video_endpoint'),
            'headers'  => [
                'Content-Type'              => 'application/json',
                'Ocp-Apim-Subscription-Key' => $bingAPIKey,
            ],
        ]);

        $this->resourceSummarizerClient = new Client([
            'base_uri' => config('ai.resource_summarizer_endpoint'),
            'headers'  => [
                'Content-Type'        => 'application/json',
                'authorization'       => $resourceSummarizerApiKey,
            ],
        ]);

        $this->projectAssessorClient = new Client([
            'base_uri' => 'https://th7cgys3gq4nz4ipzr5aekbtnu0hlywt.lambda-url.ca-central-1.on.aws/',
            'headers'  => [
                'Content-Type'  => 'application/json',
                'authorization' => $projectAssessorApiKey,
            ],
            'timeout'  => .5,
        ]);
    }

    public function createChallengeUsingAIPreview($request)
    {
        try {
            $attempt = 0;
            $validChallenges = [];

            // $language = $request->language;

            // Extract job IDs and titles
            $jobIdsArray = array_column($request['jobs'], 'key');
            $jobTitlesArray = array_column($request['jobs'], 'value');
            $jobTitles = implode(', ', $jobTitlesArray);

            // Extract skill titles
            $skillTitlesArray = array_column($request['skills'], 'value');
            $skillTitles = implode(', ', $skillTitlesArray);

            // Extract duration and level IDs and titles
            $durationID = $request['duration_id'][0]['key'];
            $durationTitle = $request['duration_id'][0]['value'];

            $levelID = $request['level_id'][0]['key'];
            $levelTitle = $request['level_id'][0]['value'];

            $additionalInformation = $request['additional_information'] ?? 'No additional information';

            $categoryTitles = Category::pluck('title')->implode(', ');

            while ($attempt < 3 && count($validChallenges) < 2) {
                $attempt++;
                $openAIResponse = $this->fetchChallengesByOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles);

                if (!$openAIResponse || empty($openAIResponse['choices'])) {
                    continue;
                }

                foreach ($openAIResponse['choices'] as $choice) {
                    $challenge = json_decode($choice['message']['content'], true);

                    // // Checks for duplicate names in all challenges so no duplicate titles would exist
                    // if (is_array($challenge) && isset($challenge['challengeTitle'])) {
                    //     if (Challenge::where('title', $challenge['challengeTitle'])->exists()) {
                    //         continue;
                    //     }
                    // }

                    if (empty($challenge['skills'])) {
                        continue;
                    }

                    $updatedSkills = $this->processSkills($challenge['skills']);
                    $updatedSkills = array_values($updatedSkills);
                    $mergedSkills = array_merge($skillTitlesArray, $updatedSkills);

                    // Making sure each challenge has more than 5 verified skill
                    if (count($mergedSkills) < 5 || !isset($challenge['challengeTitle'])) {
                        continue;
                    }

                    $orderedTitles = implode(',', array_fill(0, count($mergedSkills), '?'));
                    $skills = Skill::whereIn('title', $mergedSkills)
                        ->orderByRaw("FIELD(title, $orderedTitles)", $mergedSkills)
                        ->get(['id', 'title']);
                    $skillIds = $skills->pluck('id')->toArray();
                    $skillTitles = array_unique($skills->pluck('title')->toArray());

                    $categoryID = Category::where('title', $challenge['category'])->pluck('id')->first();

                    $challenge['level'] = $levelTitle;
                    $challenge['level_id'] = $levelID;
                    $challenge['duration'] = $durationTitle;
                    $challenge['duration_id'] = $durationID;
                    $challenge['is_ai_created'] = $request->is_ai_created;
                    $challenge['skill_titles'] = $skillTitles;
                    $challenge['skills'] = $skillIds;
                    $challenge['job_titles'] = $jobTitlesArray;
                    $challenge['jobs'] = $jobIdsArray;
                    $challenge['resource_modules'] = $request->resource_modules;
                    $challenge['resource_module_prepr'] = $request->resource_module_prepr;
                    $challenge['resource_module_openai'] = $request->resource_module_openai;
                    $challenge['resource_module_go1'] = $request->resource_module_go1;
                    $challenge['openai_resource_module_types'] = $request->openai_resource_module_types;
                    $challenge['go1_resource_module_types'] = $request->go1_resource_module_types;
                    $challenge['category_id'] = $categoryID;

                    $validChallenges[] = $challenge;
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

    public function createChallengeFromResourceUsingAIPreview($request)
    {
        try {
            $attempt = 0;
            $validChallenges = [];

            // $language = $request->language;

            $additionalInformation = $request['additional_information'] ?? 'No additional information';

            $categoryTitles = Category::pluck('title')->map(function ($title) {
                return '"'.$title.'"';
            })->implode(', ');

            $levelTitles = Levels::pluck('title')->map(function ($title) {
                return '"'.$title.'"';
            })->implode(', ');

            $durationTitles = Duration::pluck('title')->map(function ($title) {
                return '"'.$title.'"';
            })->implode(', ');

            $resourceModules = ResourceModule::whereIn('uuid', $request->resource_modules)
                ->get();

            $resourceModulesTitlesAndDescriptions = array_map(function ($module) {
                return [
                    'title'       => $module['title'],
                    'description' => $module['description'],
                ];
            }, $resourceModules->toArray());

            $resourceModulesTitlesAndDescriptions = json_encode($resourceModulesTitlesAndDescriptions);

            $resourceModulesDetails = ResourceModuleDetail::whereIn('resource_module_id', $resourceModules->pluck('id'))->get(['type', 'path']);

            $items = [];

            foreach ($resourceModulesDetails as $detail) {
                $url = $detail->path;

                // Check for YouTube URLs embedded in iframe tags and capture only the video ID
                if (preg_match('/<iframe.*src="https?:\/\/www\.youtube\.com\/embed\/([\w\-_]+)(\?.*)?".*<\/iframe>/i', $url, $match)) {
                    $url = 'https://www.youtube.com/watch?v='.$match[1];
                    $type = 'youtube_video';
                } elseif (preg_match('/^https?:\/\/www\.youtube\.com\/watch\?v=([\w\-_]+)(\?.*)?$/i', $url, $match)) {
                    $url = 'https://www.youtube.com/watch?v='.$match[1];
                    $type = 'youtube_video';
                } elseif (preg_match('/^https?:\/\/www\.youtube\.com\/embed\/([\w\-_]+)(\?.*)?$/i', $url, $match)) {
                    $url = 'https://www.youtube.com/watch?v='.$match[1]; // Convert embed URL to watch URL
                    $type = 'youtube_video';
                } elseif (preg_match('/<iframe.*src="([^"]+)".*<\/iframe>/i', $url, $match)) {
                    $url = $match[1];
                    $type = 'video';
                } else {
                    switch ($detail->type) {
                        case 0: // document
                            $type = 'file';
                            break;
                        case 5: // url
                            $type = 'url';
                            break;
                        case 3: // embedded
                            $type = 'video';
                            break;
                        case 1: // video
                        case 2: // audio
                            $type = 'video';
                            break;
                        case 4: // embedded_audio
                            $type = 'audio';
                            break;
                        case 7: // Embedded_Cover_Video
                            $type = 'video';
                            break;
                        case 6: // image
                            $type = 'image';
                            break;
                        default:
                            $type = 'file';
                    }
                }

                // Ensure all non-YouTube and non-iframe URLs are prefixed properly
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = config('site-settings.aws_url').ltrim($url, '/');
                }

                // Remove the prefix if it's a URL
                if ($type == 'url' && strpos($url, config('site-settings.aws_url')) === 0) {
                    $url = substr($url, strlen(config('site-settings.aws_url')));
                }

                $items[] = ['url' => $url, 'type' => $type];
            }

            if (empty($items)) {
                $resourceModulesSummary = '';
            } else {
                $finalObject = ['items' => $items];
                $resourceModulesSummary = $this->resourceSummarizer($finalObject)['summary'];
            }

            while ($attempt < 3 && count($validChallenges) < 2) {
                $attempt++;
                $openAIResponse = $this->fetchChallengesFromResourcesByOpenAI($durationTitles, $levelTitles, $additionalInformation, $categoryTitles, $resourceModulesTitlesAndDescriptions, $resourceModulesSummary);

                if (!$openAIResponse || empty($openAIResponse['choices'])) {
                    continue;
                }

                foreach ($openAIResponse['choices'] as $choice) {
                    $challenge = json_decode($choice['message']['content'], true);

                    if (empty($challenge['skills'])) {
                        continue;
                    }

                    $updatedSkills = $this->processSkills($challenge['skills']);
                    $updatedSkills = array_values($updatedSkills);

                    // Making sure each challenge has more than 5 verified skill
                    if (count($updatedSkills) < 5 || !isset($challenge['challengeTitle'])) {
                        continue;
                    }

                    $orderedTitles = implode(',', array_fill(0, count($updatedSkills), '?'));
                    $skills = Skill::whereIn('title', $updatedSkills)
                        ->orderByRaw("FIELD(title, $orderedTitles)", $updatedSkills)
                        ->get(['id', 'title']);
                    $skillIds = $skills->pluck('id')->toArray();
                    $skillTitles = array_unique($skills->pluck('title')->toArray());

                    $categoryID = Category::where('title', $challenge['category'])->pluck('id')->first();
                    $levelID = Levels::where('title', $challenge['level'])->pluck('id')->first();
                    $durationID = Duration::where('title', $challenge['duration'])->pluck('id')->first();

                    $challenge['level_id'] = $levelID;
                    $challenge['duration_id'] = $durationID;
                    $challenge['is_ai_created'] = $request->is_ai_created;
                    $challenge['skill_titles'] = $skillTitles;
                    $challenge['skills'] = $skillIds;
                    $challenge['category_id'] = $categoryID;

                    $validChallenges[] = $challenge;
                }
            }

            if (count($validChallenges) < 2) {
                throw new Exception('Failed to generate sufficient valid challenges.');
            }

            return $validChallenges;
        } catch (Exception $e) {
            Log::error('Error in createChallengeFromResourceUsingAIPreview in AIService.php: '.$e->getMessage());

            return false;
        }
    }

    public function resourceSummarizer($data)
    {
        $response = $this->resourceSummarizerClient->request('POST', '', [
            'json' => $data,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function createLabUsingAIPreview($request)
    {
        try {
            $attempt = 0;
            $validLabs = [];

            // $language = $request->language;

            // Extract job IDs and titles
            $jobIdsArray = array_column($request['jobs'], 'key');
            $jobTitlesArray = array_column($request['jobs'], 'value');
            $jobTitles = implode(', ', $jobTitlesArray);

            // Extract skill titles
            $skillTitlesArray = array_column($request['skills'], 'value');
            $skillTitles = implode(', ', $skillTitlesArray);

            // Extract duration and level IDs and titles
            $durationID = $request['duration_id'][0]['key'];
            $durationTitle = $request['duration_id'][0]['value'];

            $levelID = $request['level_id'][0]['key'];
            $levelTitle = $request['level_id'][0]['value'];

            $additionalInformation = $request['additional_information'] ?? 'No additional information';

            $categoryTitles = Category::pluck('title')->implode(', ');

            while ($attempt < 3 && count($validLabs) < 2) {
                $attempt++;

                $openAIResponse = $this->fetchChallengesForLabByOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles);

                if (!$openAIResponse || empty($openAIResponse['choices'])) {
                    continue;
                }

                foreach ($openAIResponse['choices'] as $choice) {
                    $lab = json_decode($choice['message']['content'], true);
                    // // Checks for duplicate names in all labs so no duplicate titles would exist
                    // if (is_array($lab) && isset($lab['labTitle'])) {
                    //     if (Lab::where('title', $lab['labTitle'])->exists()) {
                    //         continue;
                    //     }
                    // }

                    if (isset($lab['challenges']) && is_array($lab['challenges'])) {
                        $allChallengesValid = true;
                        $processedChallenges = [];

                        foreach ($lab['challenges'] as $challenge) {
                            if (isset($challenge['challengeTitle']) && Challenge::where('title', $challenge['challengeTitle'])->exists()) {
                                $allChallengesValid = false;
                                continue;
                            }

                            if (empty($challenge['skills']) || count($challenge['skills']) < 4) {
                                $allChallengesValid = false;
                                continue;
                            }

                            $updatedSkills = $this->processSkills($challenge['skills']);
                            $updatedSkills = array_values($updatedSkills);
                            $mergedSkills = array_merge($skillTitlesArray, $updatedSkills);
                            // Making sure each challenge has more than 5 verified skill

                            if (count($mergedSkills) < 5 || !isset($challenge['challengeTitle'])) {
                                continue;
                            }

                            $escapedSkills = array_map('addslashes', $mergedSkills);

                            $orderedTitles = implode(',', array_fill(0, count($escapedSkills), '?'));
                            $skills = Skill::whereIn('title', $escapedSkills)
                                ->orderByRaw("FIELD(title, $orderedTitles)", $escapedSkills)
                                ->get(['id', 'title']);
                            $skillIds = $skills->pluck('id')->toArray();
                            $skillTitles = array_unique($skills->pluck('title')->toArray());

                            $categoryID = Category::where('title', $challenge['category'])->pluck('id')->first();

                            $challenge = array_merge($challenge, [
                                'level'                         => $levelTitle,
                                'level_id'                      => $levelID,
                                'duration'                      => $durationTitle,
                                'duration_id'                   => $durationID,
                                'is_ai_created'                 => $request->is_ai_created,
                                'skill_titles'                  => $skillTitles,
                                'skills'                        => $skillIds,
                                'job_titles'                    => $jobTitlesArray,
                                'jobs'                          => $jobIdsArray,
                                'category_id'                   => $categoryID,
                                'resource_modules'              => $request->resource_modules,
                                'resource_module_prepr'         => $request->resource_module_prepr,
                                'resource_module_openai'        => $request->resource_module_openai,
                                'resource_module_go1'           => $request->resource_module_go1,
                                'openai_resource_module_types'  => $request->openai_resource_module_types,
                                'go1_resource_module_types'     => $request->go1_resource_module_types,
                                'added'                         => true,
                            ]);

                            $processedChallenges[] = $challenge;
                        }

                        if ($allChallengesValid && !empty($processedChallenges)) {
                            $validLabs[] = [
                                'labTitle'                      => $lab['labTitle'],
                                'labDescription'                => $lab['labDescription'],
                                'challenges'                    => $processedChallenges,
                                'level'                         => $levelTitle,
                                'level_id'                      => $levelID,
                                'duration'                      => $durationTitle,
                                'duration_id'                   => $durationID,
                                'is_ai_created'                 => $request->is_ai_created,
                                'skill_titles'                  => $skillTitles,
                                'skills'                        => $skillIds,
                                'job_titles'                    => $jobTitlesArray,
                                'jobs'                          => $jobIdsArray,
                                'category_id'                   => $categoryID,
                                'resource_modules'              => $request->resource_modules,
                                'resource_module_prepr'         => $request->resource_module_prepr,
                                'resource_module_openai'        => $request->resource_module_openai,
                                'resource_module_go1'           => $request->resource_module_go1,
                                'openai_resource_module_types'  => $request->openai_resource_module_types,
                                'go1_resource_module_types'     => $request->go1_resource_module_types,
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
            Log::error('Error in createLabUsingAIPreview: '.$e->getMessage());

            return false;
        }
    }

    protected function fetchChallengesByOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles)
    {
        try {
            $jobTitlesStr = is_array($jobTitles) ? implode(', ', $jobTitles) : $jobTitles;
            $skillTitlesStr = is_array($skillTitles) ? implode(', ', $skillTitles) : $skillTitles;
            $categoryTitlesStr = is_array($categoryTitles) ? implode(', ', $categoryTitles) : $categoryTitles;

            $payload = [
                'model'    => 'gpt-3.5-turbo',
                'n'        => 10,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => '
                            Please create an educational challenge tailored for the following careers: "'.$jobTitlesStr.'", focusing on the specified skills: "'.$skillTitlesStr.'", designed for "'.$levelTitle.'" level, with a duration of "'.$durationTitle.'". Additional prioritized information includes: ("'.$additionalInformation.'").
                            1. **Challenge Title**: Develop a succinct and creative title for the challenge.
                            2. **Challenge Description**: Present a paragraph describing the challenge and provide a detailed, step-by-step guide in HTML format suitable for online implementation.
                            3. **Challenge Steps**: List the exact steps mentioned in the description in an array format.
                            4. **Essential Skills**: Enumerate 10 essential skills required for this challenge, with emphasis on the provided and important skills.
                            5. **Category Selection**: Based on the specified careers, skills, and level, select one category from the following options: "'.$categoryTitlesStr.'".
                            6. **Reflective Questions**: Provide 5 reflective questions for participants to answer after completing the challenge. These questions should prompt participants to reflect on their approach, applied skills, encountered obstacles, and overall learning experience.
                            
                            JSON output format (Ensure precise adherence):
                            {
                                "challengeTitle": "Challenge Title",
                                "challengeDescription": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p> (and so forth)",
                                "category": "Selected Category",
                                "steps": ["Step 1", "Step 2", (all steps)],
                                "skills": ["Skill 1", "Skill 2", (all skills)],
                                "reflections": ["Reflection 1", "Reflection 2", (all reflections)]
                            }
                        ',
                    ],
                ],
            ];            

            $retry = 0;
            $maxRetries = 1;

            do {
                try {
                    $response = $this->openAIClient->post('', ['json' => $payload]);
                    break;
                } catch (Exception $e) {
                    if ($retry >= $maxRetries) {
                        throw new Exception('OpenAI call failed: '.$e->getMessage());
                    }
                    $retry++;

                    usleep(500000);
                }
            } while ($retry <= $maxRetries);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error('Error in fetchChallengesByOpenAI in AIService.php: '.$e->getMessage());

            return false;
        }
    }

    protected function fetchChallengesFromResourcesByOpenAI($durationTitles, $levelTitles, $additionalInformation, $categoryTitles, $resourceModulesTitlesAndDescriptions, $resourceModulesSummary)
    {
        try {
            $categoryTitlesStr = is_array($categoryTitles) ? implode(', ', $categoryTitles) : $categoryTitles;

            $payload = [
                'model'    => 'gpt-3.5-turbo',
                'n'        => 10,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => '
                            Please craft a distinct educational challenge utilizing the insights provided in the summaries of one or more resource modules. Emphasize any additional information as follows: ("'.$additionalInformation.'").
                            1. **Challenge Title**: Create an inventive title for the challenge.
                            2. **Challenge Description**: Compose a paragraph describing the challenge and provide a detailed, step-by-step guide in HTML format suitable for online implementation.
                            3. **Challenge Steps**: List the exact steps mentioned in the description in an array.
                            4. **Essential Skills**: Enumerate 10 crucial skills necessary for this challenge, with priority given to the provided and important skills.
                            5. **Category Selection**: Choose one category from the following options: "'.$categoryTitlesStr.'".
                            6. **Reflective Questions**: Provide 5 questions for participants to contemplate after completing the challenge, focusing on their approach, applied skills, encountered obstacles, and overall learning experience.
                            7. **Challenge Level**: Specify the difficulty level of the challenge: "'.$levelTitles.'".
                            8. **Challenge Duration**: Determine the duration for the challenge to end: "'.$durationTitles.'".
            
                            JSON output format (Ensure precise adherence):
                            {
                                "challengeTitle": "Challenge Title",
                                "challengeDescription": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p> (and so forth)",
                                "category": "Selected Category",
                                "steps": ["Step 1", "Step 2", (all steps)],
                                "skills": ["Skill 1", "Skill 2", (all skills)],
                                "reflections": ["Reflection 1", "Reflection 2", (all reflections)],
                                "level": "Selected Level",
                                "duration": "Selected Duration"
                            }
            
                            Utilize the titles and descriptions of the resource modules creatively: "'.$resourceModulesTitlesAndDescriptions.'".
                            Summarize the contents of the resource modules: "'.$resourceModulesSummary.'".
                        ',
                    ],
                ],
            ];            

            $retry = 0;
            $maxRetries = 1;

            do {
                try {
                    $response = $this->openAIClient->post('', ['json' => $payload]);
                    break;
                } catch (Exception $e) {
                    if ($retry >= $maxRetries) {
                        throw new Exception('OpenAI call failed: '.$e->getMessage());
                    }
                    $retry++;

                    usleep(500000);
                }
            } while ($retry <= $maxRetries);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error('Error in fetchChallengesFromResourcesByOpenAI in AIService.php: '.$e->getMessage());

            return false;
        }
    }

    protected function fetchChallengesForLabByOpenAI($jobTitles, $skillTitles, $durationTitle, $levelTitle, $additionalInformation, $categoryTitles)
    {
        try {
            $jobTitlesStr = is_array($jobTitles) ? implode(', ', $jobTitles) : $jobTitles;
            $skillTitlesStr = is_array($skillTitles) ? implode(', ', $skillTitles) : $skillTitles;
            $categoryTitlesStr = is_array($categoryTitles) ? implode(', ', $categoryTitles) : $categoryTitles;

            $payload = [
                'model'    => 'gpt-3.5-turbo',
                'n'        => 10,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => '
                            Please design an educational lab comprising 5 sequential challenges tailored for the following careers: "'.$jobTitlesStr.'", focusing on the specified skills: "'.$skillTitlesStr.'", designed for "'.$levelTitle.'" level, with a duration of "'.$durationTitle.'" for the lab to finish. Additional prioritized information includes: ("'.$additionalInformation.'"). The challenges must be arranged in order and preferably build upon each other to achieve the lab\'s objective.
                            1. **Challenge Title**: Devise a brief and creative title for the challenge without numbering (e.g., avoid "Challenge 1," "Challenge 2," etc.). Only provide the title.
                            2. **Challenge Description**: Present a paragraph describing the challenge and provide a detailed, step-by-step guide in HTML format suitable for online implementation.
                            3. **Challenge Steps**: List the exact steps mentioned in the description in an array format.
                            4. **Essential Skills**: Enumerate 10 vital skills required for this challenge, prioritizing the provided and important skills.
                            5. **Category Selection**: Based on the specified careers, skills, and level, select one category from the following options: "'.$categoryTitlesStr.'".
                            6. **Reflective Questions**: Provide 5 reflective questions for participants to answer after completing the challenge. These questions should encourage participants to reflect on their approach, applied skills, encountered obstacles, and overall learning experience.
                            7. **Lab Title**: Craft a concise title for the lab.
                            8. **Lab Description**: Provide a paragraph description about the lab and its focus.
            
                            JSON output format (Ensure precise adherence):
                            {
                                "labTitle": "Lab Title",
                                "labDescription": "Lab Description",
                                "challenges": [
                                    {
                                        "challengeTitle": "Creative Challenge Title",
                                        "challengeDescription": "<p>Brief Challenge Description</p><br /><p>1. Initial Step.</p><p>2. Next Step.</p> (and so forth)",
                                        "category": "Selected Category",
                                        "steps": ["Step 1", "Step 2", (all steps)],
                                        "skills": ["Skill 1", "Skill 2", (all skills)],
                                        "reflections": ["Reflection 1", "Reflection 2", (all reflections)]
                                    },
                                    ...
                                    (5 challenges)
                                ]
                            }
                        ',
                    ],
                ],
            ];            

            $retry = 0;
            $maxRetries = 1;

            do {
                try {
                    $response = $this->openAIClient->post('', ['json' => $payload]);
                    break;
                } catch (Exception $e) {
                    if ($retry >= $maxRetries) {
                        throw new Exception('OpenAI call failed: '.$e->getMessage());
                    }
                    $retry++;

                    usleep(500000);
                }
            } while ($retry <= $maxRetries);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error('Error in fetchChallengesForLabByOpenAI in AIService.php: '.$e->getMessage());

            return false;
        }
    }

    protected function processSkills($skills, $score = 0.92)
    {
        $updatedSkills = [];
        $lowercaseSkills = array_map('strtolower', $skills);

        try {
            $recommendationResponse = RecommendationEngineHelper::getRelatedPreprSkills($lowercaseSkills);
            foreach ($recommendationResponse as $skill) {
                if (is_array($skill)) {
                    $highestScoreSkill = $this->selectHighestScoreSkill($skill);
                    if ($highestScoreSkill['score'] >= $score) {
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
        $title = $request->challengeTitle ?? $request->labTitle ?? '';

        $language = $request->language;

        $skillIDsArray = $request->skills;

        $skillTitles = is_array($request->skill_titles) ? implode(', ', $request->skill_titles) : '';

        $jobTitles = is_array($request->job_titles) ? implode(', ', $request->job_titles) : '';

        $additionalInformation = $request['additional_information'] ?? '';

        $durationID = $request->duration_id;
        $durationTitle = $request->duration;

        $levelID = $request->level_id;
        $levelTitle = $request->level;

        $aiCombinedGroups = [];

        if ($request->resource_module_openai) {
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
                            $queryParts = [];

                            // Only add to query if the value is not empty
                            if (!empty($title)) {
                                $queryParts[] = 'Articles about '.$title;
                            }
                            if (!empty($levelTitle)) {
                                $queryParts[] = 'for level '.$levelTitle;
                            }
                            if (!empty($durationTitle)) {
                                $queryParts[] = 'and duration '.$durationTitle;
                            }
                            if (!empty($skillTitles)) {
                                $queryParts[] = 'for skills '.$skillTitles;
                            }
                            if (!empty($jobTitles)) {
                                $queryParts[] = 'for jobs '.$jobTitles;
                            }
                            if (!empty($additionalInformation)) {
                                $queryParts[] = '('.$additionalInformation.')';
                            }

                            $queryString = implode(' ', $queryParts);

                            $articleResponse = $this->bingArticleClient->request('GET', '', [
                                'query' => [
                                    'q'     => $queryString,
                                    'count' => 20,
                                ],
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
                            $videoQueryParts = [];

                            // Only add to query if the value is not empty
                            if (!empty($title)) {
                                $videoQueryParts[] = 'Videos about '.$title;
                            }
                            if (!empty($levelTitle)) {
                                $videoQueryParts[] = 'for level '.$levelTitle;
                            }
                            if (!empty($durationTitle)) {
                                $videoQueryParts[] = 'and duration '.$durationTitle;
                            }
                            if (!empty($skillTitles)) {
                                $videoQueryParts[] = 'for skills '.$skillTitles;
                            }
                            if (!empty($jobTitles)) {
                                $videoQueryParts[] = 'for jobs '.$jobTitles;
                            }
                            if (!empty($additionalInformation)) {
                                $queryParts[] = '('.$additionalInformation.')';
                            }

                            $videoQueryString = implode(' ', $videoQueryParts);

                            $videoResponse = $this->bingVideoClient->request('GET', '', [
                                'query' => [
                                    'q'     => $videoQueryString,
                                    'count' => 20,
                                ],
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
                                if (!empty($videoData['title']) && strlen($videoData['title']) < 100) {
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

                    if (($collectArticles ? $articlesCollected : true) && ($collectVideos ? $videosCollected : true)) {
                        throw new Exception('Error in gathering enough data!');
                    }
                }
            } catch (Exception $e) {
                Log::warning("Warning in createResourceModuleUsingAIPreview in attempt $attempts in AIService.php: ".$e->getMessage());
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

                        $prompt = "For each group described below, generate a creative title and a super brief complete description. Format your response as a JSON object with a 'results' key containing an array of objects, each with 'title' and 'description' keys: ".$combinedChunkDescription.
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

                        $retry = 0;
                        $maxRetries = 1;

                        do {
                            try {
                                $response = $this->openAIClient->post('', ['json' => $payload]);
                                break;
                            } catch (Exception $e) {
                                if ($retry >= $maxRetries) {
                                    throw new Exception('OpenAI call failed: '.$e->getMessage());
                                }
                                $retry++;

                                usleep(500000);
                            }
                        } while ($retry <= $maxRetries);

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
                            $resourceModule = $allAiResults[$index];

                            $newResourceModule = [];

                            if (is_array($resourceModule) && isset($resourceModule['title'])) {
                                // // Convert the title to lowercase and check if it already exists in ResourceModule
                                // if (ResourceModule::where('title', $resourceModule['title'])->exists()) {
                                //     // If the title already exists, set title to 'Resource Module'
                                //     $newResourceModule['title'] = 'Resource Module';
                                // } else {
                                //     // If the title does not exist, use the title from $allAiResults[$index]
                                $newResourceModule['title'] = $resourceModule['title'];
                            // }
                            } else {
                                // If $resourceModule is not an array or does not have a title, use default 'Resource Module'
                                $newResourceModule['title'] = 'Resource Module';
                            }

                            $group['title'] = $newResourceModule['title'];
                            $group['description'] = $resourceModule['description'] ?? 'Resource Module';
                        }

                        $group['skill_titles'] = $request->skill_titles;
                        $group['skills'] = $request->skills;
                        $group['level'] = $levelTitle;
                        $group['level_id'] = $levelID;
                        $group['duration'] = $durationTitle;
                        $group['duration_id'] = $durationID;
                        $group['is_ai_created'] = $request->is_ai_created;
                    }
                    unset($group);
                } catch (Exception $e) {
                    Log::error('Error in createResourceModuleUsingAIPreview in AIService.php: '.$e->getMessage());
                }
            }
        }

        $prepr_resource_modules = [];

        if ($request->resource_module_prepr) {
            $firstThreeSkills = array_slice($skillIDsArray, 0, 3);

            $modules = ResourceModule::whereNull('deleted_at')
                ->where('is_global', 1)
                ->where('language', $language)
                ->whereHas('skills', function ($query) use ($firstThreeSkills) {
                    $query->whereIn('foreign_id', $firstThreeSkills)
                        ->where('type', '0');
                })
                ->with(['skills'])
                ->get();

            $filteredModules = $modules->filter(function ($module) use ($firstThreeSkills) {
                $moduleSkills = $module->skills->pluck('foreign_id')->toArray();

                return count(array_intersect($moduleSkills, $firstThreeSkills)) >= 1;
            });

            $sortedModules = $filteredModules->sortByDesc(function ($module) use ($firstThreeSkills) {
                $moduleSkills = $module->skills->pluck('foreign_id')->toArray();

                return count(array_intersect($moduleSkills, $firstThreeSkills));
            });

            $topModules = $sortedModules->take(6);

            foreach ($topModules as $module) {
                $skillIds = $module->skills->pluck('foreign_id')->toArray();
                $skillTitles = Skill::findMany($skillIds)->pluck('title')->toArray();
                $level = Levels::find($module->level_id);
                $duration = Duration::find($module->duration_id);

                $prepr_resource_modules[] = [
                    'uuid'         => $module->uuid,
                    'title'        => $module->title,
                    'description'  => $module->description,
                    'skill_titles' => $skillTitles,
                    'skills'       => $skillIds,
                    'level'        => $level ? $level->title : null,
                    'level_id'     => $module->level_id,
                    'duration'     => $duration ? $duration->title : null,
                    'duration_id'  => $module->duration_id,
                    'slug'         => $module->slug,
                    'cover_image'  => !Str::endsWith($module->media, config('site-settings.default_resource_module_cover_image')) ? $module->media : null,
                    'from_prepr'   => true,
                ];
            }
        }

        $go1_resource_modules = [];

        if ($request->resource_module_go1) {
            $response = null;

            try {
                $memberManagement = new MemberManagementService();
                if (!$memberManagement->canCreateGO1Resource()) {
                    throw new Exception('No go1 access!');
                }

                $queryParts = [
                    'Challenge Title: '.($request['challengeTitle'] ?? 'N/A'),
                    'Category: '.($request['category'] ?? 'N/A'),
                    'Level: '.($request['level'] ?? 'N/A'),
                    'Duration: '.($request['duration'] ?? 'N/A'),
                ];

                if (!empty($request['skill_titles'])) {
                    $queryParts[] = 'Skills: ('.implode(', ', $request['skill_titles']).')';
                }
                if (!empty($request['job_titles'])) {
                    $queryParts[] = 'Jobs: ('.implode(', ', $request['job_titles']).')';
                }
                if (!empty($request['steps'])) {
                    $queryParts[] = 'Steps: ('.implode(', ', $request['steps']).')';
                }

                $fullQueryString = implode(', ', $queryParts).'.';
                $payload = [
                    'model'    => 'gpt-3.5-turbo',
                    'n'        => 1,
                    'messages' => [
                        [
                            'role'    => 'user',
                            'content' => 'According to the following information, I want you to find 3 most relevant keywords to them. Pint exactly at the main topics of it not something general. '.$fullQueryString.' Output format: { "keywords": ["Keyword 1", "Keyword 2", "Keyword 3"] }',
                        ],
                    ],
                ];

                $retry = 0;
                $maxRetries = 1;

                do {
                    try {
                        $apiResponse = $this->openAIClient->post('', ['json' => $payload]);
                        $response = json_decode($apiResponse->getBody()->getContents(), true);
                        break;
                    } catch (Exception $e) {
                        if ($retry >= $maxRetries) {
                            throw new Exception($e->getMessage());
                        }
                        $retry++;

                        usleep(500000);
                    }
                } while ($retry <= $maxRetries);

                if (!$response || empty($response['choices'])) {
                    throw new Exception('No choices in the response');
                }

                $responseContent = json_decode($response['choices'][0]['message']['content'], true);
                $keywords = $responseContent['keywords'] ?? [];

                foreach ($request->go1_resource_module_types as $type) {
                    foreach ($keywords as $keyword) {
                        try {
                            $queryParams = http_build_query(
                                [
                                    'keyword'    => $keyword,
                                    'sort'       => 'relevance',
                                    'type'       => $type,
                                    'limit'      => '15',
                                    'offset'     => 0,
                                    'language[]' => 'en',
                                ]
                            );

                            $response = GO1Helper::listResources($queryParams);

                            if (is_array($response) && isset($response['hits']) && is_array($response['hits'])) {
                                $count = 0;
                                foreach ($response['hits'] as $item) {
                                    if ($count < 3) {
                                        $module = [];

                                        if (!isset($item['title']) || !isset($item['description']) || !isset($item['skills'])) {
                                            break;
                                        }

                                        $module['id'] = $item['id'] ?? null;
                                        $module['type'] = $item['type'] ?? null;
                                        $module['title'] = $item['title'];
                                        $module['published'] = $item['published'] ?? null;
                                        $module['description'] = $item['description'];
                                        $module['image'] = $item['image'] ?? null;
                                        $module['created_time'] = $item['created_time'] ?? null;
                                        $module['updated_time'] = $item['updated_time'] ?? null;
                                        $module['decommission_time'] = $item['decommission_time'] ?? null;
                                        $module['remove_time'] = $item['remove_time'] ?? null;
                                        $module['language'] = $item['language'] ?? null;
                                        $module['tags'] = $item['tags'] ?? null;
                                        $module['delivery'] = $item['delivery'] ?? null;
                                        $module['pricing'] = $item['pricing'] ?? null;
                                        $module['provider'] = $item['provider'] ?? null;
                                        $module['subscription'] = $item['subscription'] ?? null;
                                        $module['items'] = $item['items'] ?? null;
                                        $module['items_count'] = $item['items_count'] ?? null;
                                        $module['assessable'] = $item['assessable'] ?? null;
                                        $module['collections'] = $item['collections'] ?? null;
                                        $module['attributes'] = $item['attributes'] ?? null;
                                        $module['summary'] = $item['summary'] ?? null;
                                        $module['previewable'] = $item['previewable'] ?? null;
                                        $module['authors'] = $item['authors'] ?? null;
                                        $module['ratings'] = $item['ratings'] ?? null;
                                        $module['from_go1'] = true;

                                        $skills = Arr::pluck($item['skills'] ?? [], 'name');
                                        $processedSkills = $this->processSkills($skills, 0.75);

                                        $item['skills'] = array_map(function ($name) {
                                            return ['name' => $name];
                                        }, $processedSkills);

                                        $module['skills'] = $item['skills'] ?? null;
                                        $go1_resource_modules[] = $module;
                                        $count++;
                                    } else {
                                        break;
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            Log::error("API call failed for type {$type} and keyword {$keyword}: ".$e->getMessage());
                            continue;
                        }
                    }
                }
            } catch (Exception $e) {
                Log::warning('Error in createResourceModuleUsingAIPreview in AIService.php: '.$e->getMessage());

                return false;
            }
        }

        $combinedModules = array_merge($prepr_resource_modules, $aiCombinedGroups, $go1_resource_modules);

        shuffle($combinedModules);

        $shuffledModules = $combinedModules;

        return $shuffledModules;
    }

    public function addAIProjectEvaluation($challengeAssessment, $projectData, $userData, $request)
    {
        try {
            $criteria = collect($challengeAssessment)->map(function ($item) {
                return [
                    'id'          => $item['id'],
                    'title'       => $item['title'],
                    'description' => $item['description'] ?? null,
                    'score'       => $item['score'],
                    'weight'      => $item['weight'],
                ];
            })->values()->all();

            $challengeID = $challengeAssessment[0]->challenge_id;
            $challenge = ChallengeService::getChallengeBasedOnId($challengeID);

            $projectFiles = ProjectFile::where('project_id', $projectData['id'])->get();
            $items = [];

            foreach ($projectFiles as $file) {
                $url = $file->path;
                $type = '';

                // Check for YouTube URLs embedded in iframe tags and capture only the video ID
                if (preg_match('/<iframe.*src="https?:\/\/www\.youtube\.com\/embed\/([\w\-_]+)(\?.*)?".*<\/iframe>/i', $url, $match)) {
                    $url = 'https://www.youtube.com/watch?v='.$match[1];
                    $type = 'youtube_video';
                } elseif (preg_match('/^https?:\/\/www\.youtube\.com\/watch\?v=([\w\-_]+)(\?.*)?$/i', $url, $match)) {
                    $url = 'https://www.youtube.com/watch?v='.$match[1];
                    $type = 'youtube_video';
                } elseif (preg_match('/^https?:\/\/www\.youtube\.com\/embed\/([\w\-_]+)(\?.*)?$/i', $url, $match)) {
                    $url = 'https://www.youtube.com/watch?v='.$match[1]; // Convert embed URL to watch URL
                    $type = 'youtube_video';
                } elseif (preg_match('/<iframe.*src="([^"]+)".*<\/iframe>/i', $url, $match)) {
                    $url = $match[1];
                    $type = 'video';
                } else {
                    switch ($file->type) {
                        case 'docs':
                            $type = 'file';
                            break;
                        case 'video':
                        case 'audio':
                            $type = 'video';
                            break;
                        case 'image':
                            $type = 'image';
                            break;
                        default:
                            $type = 'file';
                    }
                }

                // Ensure all non-YouTube and non-iframe URLs are prefixed properly
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = config('site-settings.aws_url').ltrim($url, '/');
                }

                // Remove the prefix if it's a URL
                if ($type == 'url' && strpos($url, config('site-settings.aws_url')) === 0) {
                    $url = substr($url, strlen(config('site-settings.aws_url')));
                }

                $items[] = ['url' => $url, 'type' => $type];
            }

            try {
                $requestBody = [
                    'language'              => 'en',
                    'status'                => 'published',
                    'app_url'               => config('app.url'),
                    'user_id'               => $userData['id'],
                    'project_id'            => $projectData['id'],
                    'project_slug'          => $projectData['slug'],
                    'project_title'         => $projectData['title'],
                    'project_description'   => preg_replace(['/[\r\n\t\x{00A0}]+/u', '/[\x{2019}\x{2018}]/u'], ['', "'"], strip_tags(html_entity_decode($projectData['description'], ENT_QUOTES | ENT_HTML5))),
                    'challenge_title'       => $challenge['title'],
                    'challenge_description' => preg_replace(['/[\r\n\t\x{00A0}]+/u', '/[\x{2019}\x{2018}]/u'], ['', "'"], strip_tags(html_entity_decode($challenge['description'], ENT_QUOTES | ENT_HTML5))),
                    'criteria'              => $criteria,
                    // We are not retrieving the below fields yet due to not having the necessary tables. 5/20/2024
                    // Give the "pitch" with "question" and "answer" just like criteria (table project_pitches)
                    'items' => $items,
                ];
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }

            try {
                $response = $this->projectAssessor($requestBody);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }

            if ($response) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('Error in addAIProjectEvaluation in AIService.php: '.$e->getMessage());

            return false;
        }
    }

    public function projectAssessor($data)
    {
        $request = new Request('POST', '', [], json_encode($data));

        try {
            $this->projectAssessorClient->send($request);
        } catch (Exception $e) {
        }

        return true;
    }
}
