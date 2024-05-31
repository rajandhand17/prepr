<?php

namespace App\Helpers;

use App\Services\Manage\GO1AccessTokenService;
use App\Services\Manage\ResourceModuleService;
use App\Services\Manage\UserResourceProgressTrackingService;
use App\Services\Manage\WebhookMetadataService;
use App\Services\Public\ResourceModuleDetailService;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GO1Helper
{
    private static function getAuthBaseUrl()
    {
        try {
            return config('go1.go1_auth_url');
        } catch (Exception $exception) {
            return false;
        }
    }

    private static function getBaseUrl()
    {
        try {
            return config('go1.go1_base_url').'/'.config('go1.go1_api_version');
        } catch (Exception $exception) {
            return false;
        }
    }

    private static function isTokenExpired($token): bool
    {
        try {
            if (!$token) {
                return true;
            }

            $payload = explode('.', $token)[1];
            $decodedPayload = base64_decode($payload);

            return time() > json_decode($decodedPayload)->exp;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function getAccessToken(): string
    {
        try {
            $accessTokenService = new GO1AccessTokenService();
            $data = $accessTokenService->getAccessToken();

            if (!$data) {
                $responseData = GO1Helper::fetchToken();
                $accessTokenService->createAccessToken($responseData['access_token']);

                return $responseData['access_token'];
            }

            $existingToken = $data->access_token ?? null;

            if (self::isTokenExpired($existingToken)) {
                $responseData = GO1Helper::fetchToken();

                if (!$responseData) {
                    return false;
                }

                $accessTokenService->updateAccessToken($responseData['access_token'], $data->id);

                return $responseData['access_token'];
            }

            return $existingToken;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function fetchToken()
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(self::getAuthBaseUrl().'/oauth/token', [
                'client_id'     => config('go1.go1_client_id'),
                'client_secret' => config('go1.go1_client_secret'),
                'grant_type'    => 'client_credentials',
            ]);

            if (!$response->ok()) {
                return false;
            }

            return $response->json();
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function findGO1UserByEmail($email)
    {
        $accessToken = self::getAccessToken();
        $endPoint = self::getBaseUrl().'/users';
        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
        ])->get($endPoint, ['email' => $email]);

        if ($response->status() >= 400) {
            return false;
        }

        if (!data_get($response->json(), 'hits.0')) {
            return false;
        }

        return data_get($response->json(), 'hits.0');
    }

    public static function findOrCreateUser($user)
    {
        try {
            $accessToken = self::getAccessToken();
            $endPoint = self::getBaseUrl().'/users';
            $users = self::findGO1UserByEmail($user['email']);

            if ($users) {
                return $users;
            }
            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken",
            ])->post($endPoint, array_merge($user, ['send_login_email' => false, 'password' => config('go1.default_user_password')]));

            if ($response->status() >= 400) {
                return false;
            }

            return $response->json();
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function listResources($queryParams = '')
    {
        try {
            $accessToken = self::getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get(self::getBaseUrl().'/learning-objects?'.$queryParams);

            if ($response->status() >= 400) {
                return false;
            }

            return $response->json();
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function playResource($id, $courseId)
    {
        try {
            $accessToken = self::getAccessToken();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
                'Accept'        => 'application/json',
            ])->post("https://api.go1.com/v2/users/{$id}/login?redirect_url=/play/$courseId");

            if ($response->status() >= 400) {
                return false;
            }

            return $response->json();
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function registerWebhookToGO1($url = '')
    {
        try {
            if (empty($url)) {
                $url = self::getDefaultUrl();
            }

            if (!self::isValidUrl($url)) {
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.self::getAccessToken(),
                'Accept'        => 'application/json',
            ])->post(self::getBaseUrl().'/webhooks', [
                'enrollment_create'    => true,
                'enrollment_delete'    => true,
                'enrollment_update'    => true,
                'lo_create'            => false,
                'lo_delete'            => false,
                'lo_update'            => false,
                'enabled'              => true,
                'url'                  => $url,
                'user_create'          => false,
                'user_delete'          => false,
                'user_update'          => false,
                'content_update'       => false,
                'content_decommission' => false,
            ]);

            if ($response->status() >= 400) {
                return false;
            }

            return $response->json();
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function getPage()
    {
        try {
            $requestQuery = request()->query();

            return isset($requestQuery['page']) ? (int) $requestQuery['page'] : 1;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function prepareGO1Query($request)
    {
        try {
            $unwantedParams = ['page', 'language', 'search'];
            $languageMap = ['en' => 'en', 'fr-CA' => 'fr'];

            $dataLimit = config('go1.go1_total_resource_data');
            $defaultPerPage = config('site-settings.pagination_per_page');
            $lastPage = ceil($dataLimit / $defaultPerPage);
            $currentPage = self::getPage();
            $requestQuery = $request->query();

            $remainder = ($dataLimit % $defaultPerPage);
            $dataOnLastPage = $remainder > 0 ? $remainder : $defaultPerPage;

            $isLastPage = ($lastPage == $currentPage);

            $limit = $isLastPage ? $dataOnLastPage : $defaultPerPage;

            $offset = ($currentPage - 1) * $limit;
            $defaultQueryParams = [
                'limit'      => $limit,
                'offset'     => $offset,
                'keyword'    => $request->get('search'),
                'language[]' => $languageMap[$request->language],
            ];

            $finalQueryParams = array_merge($requestQuery, $defaultQueryParams);

            foreach ($unwantedParams as $key) {
                if (array_key_exists($key, $finalQueryParams)) {
                    unset($finalQueryParams[$key]);
                }
            }

            return $finalQueryParams;
        } catch (Exception $e) {
            Log::error('Error in prepareGO1Query in GO1Helper.php: '.$e->getMessage());

            return false;
        }
    }

    public static function removeLastSlash($url)
    {
        try {
            if (substr($url, -1) === '/') {
                $url = substr($url, 0, -1);
            }

            return $url;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function getDefaultUrl()
    {
        try {
            $appUrl = self::removeLastSlash(config('app.url'));

            return $appUrl.'/api/v1/go1/webhook';
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function isValidUrl($url): bool
    {
        try {
            return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function webhook($payload)
    {
        try {
            $go1UserId = $payload['data']['user_id'];
            $go1ResourceId = $payload['data']['lo_id'];
            $user = UserService::getUserByGO1Id($go1UserId);
            if (!$user) {
                return false;
            }
            $resource = ResourceModuleService::getResourceModuleBasedOnGO1Id($go1ResourceId);

            if (!$resource) {
                return false;
            }
            $type = $payload['type'];

            if (!($type === 'enrolment.create' || $type === 'enrolment.update')) {
                return ['message' => 'unwanted event'];
            }

            $data = UserResourceProgressTrackingService::createOrUpdate($resource->id, $user->id, $payload);
            if (!$data) {
                return false;
            }
            $parentData = $data->first();
            if (!$parentData) {
                return false;
            }

            // Tracking completion status in resource module visit
            if ($data->completion_status === 'completed') {
                $userId = $data->user_id;
                $resourceModuleId = $data->resource_module_id;
                $assetId = $data->id;
                $assetType = '8';
                $checkResourceModuleAssetVisit = ResourceModuleDetailService::checkResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType);
                if ($checkResourceModuleAssetVisit === false) {
                    $addResourceModuleAssetVisit = ResourceModuleDetailService::addResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType);
                }
            }

            WebhookMetadataService::create($type, $payload, $parentData['id']);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
