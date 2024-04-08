<?php

namespace App\Services\GO1;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModule;
use App\Models\ResourceModuleSkillsGroupsStack;
use App\Models\Skill;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\Http;

class ResourceService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPage()
    {
        $requestQuery = request()->query();

        return isset($requestQuery['page']) ? (int) $requestQuery['page'] : 1;
    }

    public function prepareGO1Query()
    {
        try {
            $unwantedParams = ['page', 'language'];

            $dataLimit = 10000;
            $defaultPerPage = 9;
            $lastPage = ceil($dataLimit / $defaultPerPage);
            $currentPage = $this->getPage();

            $requestQuery = request()->query();

            $remainder = ($dataLimit % $defaultPerPage);
            $dataOnLastPage = $remainder > 0 ? $remainder : $defaultPerPage;

            $isLastPage = ($lastPage == $currentPage);

            $limit = $isLastPage ? $dataOnLastPage : $defaultPerPage;

            $offset = ($currentPage - 1) * $limit;
            $defaultQueryParams = [
                'limit'  => $limit,
                'offset' => $offset,
            ];

            $finalQueryParams = array_merge($requestQuery, $defaultQueryParams);

            foreach ($unwantedParams as $key) {
                if (array_key_exists($key, $finalQueryParams)) {
                    unset($finalQueryParams[$key]);
                }
            }

            return $finalQueryParams;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function createResourceModule($body)
    {
        try {
            $slug = UtilityHelper::generateSlug($body['title'], ResourceModule::class);
            $resourceModule = new ResourceModule();
            $resourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $resourceModule->language = request()->language;
            $resourceModule->user_id = auth()->user()->id;
            $resourceModule->organization_id = config('go1.prepr_id');
            $resourceModule->title = $body['title'];
            $resourceModule->slug = $slug;
            $resourceModule->description = $body['description'];
            $resourceModule->privacy = config('constants.resource_module_privacy.yes');
            $resourceModule->status = config('constants.resource_module_status.draft');
            $resourceModule->is_global = config('constants.resource_module_is_global.no');
            $resourceModule->is_ai_created = config('constants.challenge_ai_created.no');
            $resourceModule->go1_course_id = $body['id'];
            $resourceModule->go1_metadata = $body;
            $resourceModule->save();

            return $resourceModule;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function storeSkills($resourceModuleId, $skills = [])
    {
        try {
            $skillsIds = array_map(function ($item) {
                $data = Skill::firstOrCreate(['title' => $item['name']]);

                return $data->id;
            }, $skills);

            if (count($skills) > 0) {
                foreach ($skillsIds as $id) {
                    $ResourceModuleGroupsStack = new ResourceModuleSkillsGroupsStack();
                    $ResourceModuleGroupsStack->resource_module_id = $resourceModuleId;
                    $ResourceModuleGroupsStack->foreign_id = $id;
                    $ResourceModuleGroupsStack->type = '0';
                    $ResourceModuleGroupsStack->save();
                }
            }

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function listResources($queryParams = '')
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get("$this->endPointBaseUrl/learning-objects?".$queryParams);

            if ($response->status() >= 400) {
                throw new Exception("Status: {$response->status()}--{$response->body()}");
            }

            return $response->json();
        } catch (Exception $exception) {
            return false;
        }
    }

    public function playResource($id, $courseId)
    {
        try {
            $accessToken = $this->getAccessToken();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
                'Accept'        => 'application/json',
            ])->post("https://api.go1.com/v2/users/{$id}/login?redirect_url=/play/$courseId");

            if ($response->status() >= 400) {
                throw new Exception("status: {$response->status()}---{$response->body()}");
            }

            return $response->json();
        } catch (Exception $exception) {
            return false;
        }
    }
}
