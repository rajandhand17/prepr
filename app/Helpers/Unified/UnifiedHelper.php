<?php

namespace App\Helpers\Unified;

use Faker\Factory;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

class UnifiedHelper extends UnifiedBaseHelper
{
    /**
     * @param array $categories
     *
     * @return false|PromiseInterface|Response
     */
    public static function getIntegrations(array $categories = ['hris']): false|PromiseInterface|Response
    {
        try {
            return self::get(
                sprintf(config('unified.url_paths.integration'), config('unified.workspace')),
                [
                    'summary'    => 'true',
                    'active'     => 'true',
                    'categories' => $categories,
                ]
            );
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function getPage()
    {
        try {
            $requestQuery = request()->query();

            return isset($requestQuery['page']) ? (int) $requestQuery['page'] : 1;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function prepareListEmployeeQuery()
    {
        try {
            $limit = config('unified.pagination_per_page');
            $currentPage = self::getPage();
            $offset = ($currentPage - 1) * $limit;

            $query = [
                'limit'  => (int) $limit,
                'offset' => $offset,
            ];
            if (request()->has('sort_by')) {
                $query['sort'] = request()->get('sort_by');
            }
            if (request()->has('search')) {
                $query['query'] = request()->get('search');
            }

            return $query;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function fakeEmployeeList()
    {
        $faker = Factory::create();
        $userLists = [];
        for ($i = 0; $i < 20; $i++) {
            $userLists[] = [
                'id'     => $faker->uuid(),
                'name'   => $faker->name(),
                'email'  => 'learnlab'.$faker->username().'@yopmail.com',
            ];
        }

        return $userLists;
    }

    public static function getEmployee($connectionId)
    {
        try {
            if (config('unified.use_faker')) {
                return self::fakeEmployeeList();
            }

            $requestParams = self::prepareListEmployeeQuery();
            $data = self::get(
                sprintf(config('unified.url_paths.employee_list'), $connectionId),
                $requestParams
            );

            return collect($data->json())->map(function ($item) {
                return [
                    'id'    => data_get($item, 'id'),
                    'name'  => data_get($item, 'name'),
                    'email' => data_get($item, 'emails.0.email'),
                ];
            });
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
