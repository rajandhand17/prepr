<?php

namespace App\Repositories\Api\GO1;

use App\Models\ResourceModule;
use App\Models\User;
use App\Services\GO1\ResourceService;
use App\Services\GO1\UserService;
use App\Services\GO1\WebhookService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GO1Repository implements GO1Interface
{
    public function __construct(private ResourceService $resourceService, private UserService $userService, private WebhookService $webhookService)
    {
    }

    public function getCourseLists()
    {
        try {
            $queryParams = http_build_query($this->resourceService->prepareGO1Query());
            $data = $this->resourceService->listResources($queryParams);
            $totalCount = min($data['total'], 10000);

            return [
                'total_count'  => $totalCount,
                'per_page'     => 9,
                'count'        => count(data_get($data, 'hits')),
                'current_page' => $this->resourceService->getPage(),
                'total_pages'  => ceil($totalCount / 9),
                'list'         => data_get($data, 'hits'),
            ];
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    public function createResourceModule($body)
    {
        try {
            return DB::transaction(function () use ($body) {
                $skills = data_get($body, 'skills') ?? [];
                $resourceModule = $this->resourceService->createResourceModule($body);
                $resourceSkills = $this->resourceService->storeSkills($resourceModule->id, $skills);

                if ($resourceModule && $resourceSkills) {
                    DB::commit();

                    return $resourceModule;
                }
                DB::rollBack();

                return false;
            });
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    public function listFilters($type)
    {
        try {
            $params = [
                'topics'    => 'facets=topics&limit=0',
                'providers' => 'facets=instance&limit=0',
            ];

            $response = $this->resourceService->listResources($params[$type]);
            $topics = data_get($response, 'facets.topics.buckets') ?? [];
            $providers = data_get($response, 'facets.instance.buckets') ?? [];

            $topics = array_map(function ($item) {
                return ['label' => $item['key'], 'doc_count' => $item['doc_count']];
            }, $topics);

            $providers = array_map(function ($item) {
                return [
                    'name'      => $item['name'] ?? '',
                    'doc_count' => $item['doc_count'] ?? '',
                    'key'       => $item['key'] ?? '',
                ];
            }, $providers);

            return [
                'topics'    => $topics,
                'providers' => $providers,
            ];
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    public function getResourceModuleBySlug($slug)
    {
        try {
            return ResourceModule::query()->where('slug', $slug)->first();
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    public function playCourse($go1CourseId)
    {
        try {
            if (!auth()->user()->go1_id) {
                $response = $this->userService->createUser([
                    'email'      => explode('@', auth()->user()->email)[0].config('go1.email_prefix').'@prepr.org',
                    'first_name' => auth()->user()->first_name,
                    'last_name'  => auth()->user()->last_name,
                ]);
                $go1UserId = $response['id'];
                User::query()->where('id', auth()->user()->id)->update(['go1_id' => $go1UserId, 'go1_user_metadata' => $response]);
            }

            $user = User::query()->where('id', auth()->user()->id)->first();

            return $this->resourceService->playResource($user->go1_id, $go1CourseId);
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    public function webhook($payload)
    {
        try {
            $this->webhookService->webhook($payload);

            return true;
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }
}
