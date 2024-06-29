<?php

namespace App\Repositories\Api\GO1;

use App\Helpers\GO1Helper;
use App\Helpers\UtilityHelper;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\ResourceModuleService;
use App\Services\UserService;
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
            $queryParams = http_build_query(GO1Helper::prepareGO1Query(request()));
            $data = GO1Helper::listResources($queryParams);
            $totalCount = min($data['total'], config('go1.go1_total_resource_data'));

            return [
                'total_count'  => (int) $totalCount,
                'per_page'     => (int) config('site-settings.pagination_per_page'),
                'count'        => count(data_get($data, 'hits')),
                'current_page' => GO1Helper::getPage(),
                'total_pages'  => ceil($totalCount / config('site-settings.pagination_per_page')),
                'list'         => data_get($data, 'hits'),
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public function createResourceModule($request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $body = $request->go1_course;
                $skills = data_get($body, 'skills') ?? [];
                $resourceModule = $this->resourceModuleService->createResourceModule($request, null, true);
                $resourceSkills = $this->resourceModuleService->storeGO1Skills($resourceModule->id, $skills);

                if ($resourceModule && $resourceSkills) {
                    DB::commit();

                    return $resourceModule;
                }
                DB::rollBack();

                return false;
            });
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
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

            $response = GO1Helper::listResources($params[$type]);
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
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public function getResourceModuleBySlug($slug)
    {
        try {
            return ResourceModuleService::getResourceModuleBasedOnSlug($slug);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public function canPlayGO1Resoruces()
    {
        try {
            return $this->memberManagementService->canPlayGO1Resoruces();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public function playCourse($go1CourseId)
    {
        try {
            if (!auth()->user()->go1_id) {
                $response = GO1Helper::findOrCreateUser([
                    'email'      => explode('@', auth()->user()->email)[0].config('go1.go1_email_prefix').'@prepr.org',
                    'first_name' => auth()->user()->first_name,
                    'last_name'  => auth()->user()->last_name,
                ]);
                $go1UserId = $response['id'];
                UserService::mapGO1User($go1UserId, $response);
            }
            $user = UserService::getUserById(auth()->user()->id);
            if (!$user) {
                return false;
            }

            return GO1Helper::playResource($user->go1_id, $go1CourseId);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public function webhook($payload)
    {
        try {
            $webhook = GO1Helper::webhook($payload);
            if (!$webhook) {
                return false;
            }

            return true;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
