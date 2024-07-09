<?php

namespace App\Services\Public\AdvanceSearch;

use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ChallengeTemplate;
use App\Models\Lab;
use App\Models\LabMarketplace;
use App\Models\LabProgram;
use App\Models\Organization;
use App\Models\Project;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceModule;
use App\Models\User;
use Exception;

class AdvanceSearchService
{
    /**
     * @param $resource
     *
     * @return array|false
     */
    private function prepareMetaData($resource): false|array
    {
        try {
            return [
                'total_count'  => $resource->total(),
                'per_page'     => $resource->perPage(),
                'count'        => $resource->count(),
                'current_page' => $resource->currentPage(),
                'total_pages'  => $resource->lastPage(),
            ];
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchLab(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = Lab::query()
                ->whereSearchFilter($keyword, $filter)
                ->whereVerified()
                ->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchLabPrograms(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = LabProgram::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchLabMarketPlace(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = LabMarketplace::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchChallenges(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = Challenge::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchChallengePaths(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = ChallengePath::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));

            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchChallengeTemplates(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = ChallengeTemplate::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchResourceModules(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = ResourceModule::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchResourceGroups(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = ResourceGroup::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchResourceCollections(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = ResourceCollection::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return false|array
     */
    public function searchProjects(string|null $keyword, array $filter = []): false|array
    {
        try {
            $data = Project::query()->whereSearchFilter($keyword, $filter)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     *
     * @return false|array
     */
    public function searchOrganization(string|null $keyword): false|array
    {
        try {
            $data = Organization::query()->whereSearch($keyword)
                ->whereVerified()->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     *
     * @return false|array
     */
    public function searchUsers(string|null $keyword): false|array
    {
        try {
            $data = User::query()->
            withCount([
                'userProjects',
                'userSkills',
                'userLabs',
                'userAchievements',
            ])->with(['userPersonal', 'userSkills'])->whereSearch($keyword)->paginate(config('site-settings.pagination_per_page'));
            $metadata = $this->prepareMetaData($data);
            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }
}
