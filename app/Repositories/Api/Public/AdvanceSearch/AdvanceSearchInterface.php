<?php

namespace App\Repositories\Api\Public\AdvanceSearch;

interface AdvanceSearchInterface
{
    public function searchLab(string|null $keyword, array $filter = []): array|false;

    public function searchLabPrograms(string|null $keyword, array $filter = []): array|false;

    public function searchLabMarketPlace(string|null $keyword, array $filter = []): array|false;

    public function searchChallenges(string|null $keyword, array $filter = []): array|false;

    public function searchChallengePaths(string|null $keyword, array $filter = []): array|false;

    public function searchChallengeTemplates(string|null $keyword, array $filter = []): array|false;

    public function searchResourceModules(string|null $keyword, array $filter = []): array|false;

    public function searchResourceGroups(string|null $keyword, array $filter = []): array|false;

    public function searchResourceCollections(string|null $keyword, array $filter = []): array|false;

    public function searchProjects(string|null $keyword, array $filter = []): array|false;

    public function searchOrganization(string|null $keyword): array|false;

    public function searchUsers(string|null $keyword): array|false;
}
