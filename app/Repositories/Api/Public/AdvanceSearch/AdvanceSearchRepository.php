<?php

namespace App\Repositories\Api\Public\AdvanceSearch;

use App\Services\Public\AdvanceSearch\AdvanceSearchService;

class AdvanceSearchRepository implements AdvanceSearchInterface
{
    public function __construct(protected AdvanceSearchService $advanceSearchService)
    {
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchLab(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchLab($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchLabPrograms(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchLabPrograms($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchLabMarketPlace(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchLabMarketPlace($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchChallenges(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchChallenges($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchChallengePaths(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchChallengePaths($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchChallengeTemplates(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchChallengeTemplates($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchResourceModules(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchResourceModules($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchResourceGroups(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchResourceGroups($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchResourceCollections(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchResourceCollections($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     * @param array       $filter
     *
     * @return array|false
     */
    public function searchProjects(?string $keyword, array $filter = []): array|false
    {
        try {
            return $this->advanceSearchService->searchProjects($keyword, $filter);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     *
     * @return array|false
     */
    public function searchOrganization(?string $keyword): array|false
    {
        try {
            return $this->advanceSearchService->searchOrganization($keyword);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string|null $keyword
     *
     * @return array|false
     */
    public function searchUsers(?string $keyword): array|false
    {
        try {
            return $this->advanceSearchService->searchUsers($keyword);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
