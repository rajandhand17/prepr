<?php

namespace App\Repositories\Api\GO1;

use App\Helpers\GO1Helper;
use App\Models\ResourceModule;
use App\Models\User;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\ResourceModuleService;
use Exception;
use Illuminate\Support\Facades\DB;

class GO1Repository implements GO1Interface
{
    public function __construct(private ResourceModuleService $resourceModuleService, private MemberManagementService $memberManagementService)
    {
    }

    public function getCourseLists()
    {
        try {
            $queryParams = http_build_query(GO1Helper::prepareGO1Query());
            $data = GO1Helper::listResources($queryParams);
            $totalCount = min($data['total'], config('go1.total_resource_data'));

            return [
                'total_count' => $totalCount,
                'per_page' => config('go1.per_page'),
                'count' => count(data_get($data, 'hits')),
                'current_page' => GO1Helper::getPage(),
                'total_pages' => ceil($totalCount / config('go1.per_page')),
                'list' => data_get($data, 'hits'),
            ];
        } catch (Exception $exception) {
            return false;
        }
    }

    public function createResourceModule($body)
    {
        try {
            return DB::transaction(function () use ($body) {
                $skills = data_get($body, 'skills') ?? [];
                $resourceModule = $this->resourceModuleService->createFromGO1($body);
                $resourceSkills = $this->resourceModuleService->storeGO1Skills($resourceModule->id, $skills);

                if ($resourceModule && $resourceSkills) {
                    DB::commit();

                    return $resourceModule;
                }
                DB::rollBack();

                return false;
            });
        } catch (Exception $exception) {
            return false;
        }
    }

    public function listFilters($type)
    {
        try {
            $params = [
                'topics' => 'facets=topics&limit=0',
                'providers' => 'facets=instance&limit=0',
            ];

            $response = GO1Helper::listResources($params[$type]);
            $topics = data_get($response, 'facets.topics.buckets') ?? [];
            $providers = data_get($response, 'facets.instance.buckets') ?? [];

            $topics = array_map(function ($item) {
                return ['label' => $item['key'], 'doc_count' => $item['doc_count']];
            }, $topics);

            $providers = array_map(function ($item) {
                return [
                    'name' => $item['name'] ?? '',
                    'doc_count' => $item['doc_count'] ?? '',
                    'key' => $item['key'] ?? '',
                ];
            }, $providers);

            return [
                'topics' => $topics,
                'providers' => $providers,
            ];
        } catch (Exception $exception) {
            return false;
        }
    }

    public function getResourceModuleBySlug($slug)
    {
        try {
            return ResourceModuleService::getResourceModuleBasedOnSlug($slug);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function canPlayGO1Resoruces()
    {
        try {
            return $this->memberManagementService->canPlayGO1Resoruces();
        } catch (Exception $exception) {
            return false;
        }
    }

    public function playCourse($go1CourseId)
    {
        try {
            if (!auth()->user()->go1_id) {
                $response = GO1Helper::createUser([
                    'email' => explode('@', auth()->user()->email)[0] . config('go1.email_prefix') . '@prepr.org',
                    'first_name' => auth()->user()->first_name,
                    'last_name' => auth()->user()->last_name,
                ]);
                $go1UserId = $response['id'];
                User::query()->where('id', auth()->user()->id)->update(['go1_id' => $go1UserId, 'go1_user_metadata' => $response]);
            }

            $user = User::query()->where('id', auth()->user()->id)->first();

            return GO1Helper::playResource($user->go1_id, $go1CourseId);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function webhook($payload)
    {
        try {
            GO1Helper::webhook($payload);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
