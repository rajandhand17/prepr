<?php

namespace App\Helpers\Unified;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UnifiedBaseHelper
{
    /**
     * @param string $url
     * @param array  $params
     * @param array  $headers
     * @param int    $iteration
     *
     * @return false|PromiseInterface|Response
     */
    public static function get(string $url, array $params = [], array $headers = [], int $iteration = 1): false|PromiseInterface|Response
    {
        try {
            $response = Http::baseUrl(config('unified.base_url'))
                ->withToken(config('unified.key'))
                ->acceptJson()
                ->withQueryParameters($params)
                ->withHeaders($headers)->get($url);

            if (!$response->ok()) {
                if ($iteration === 1) {
                    return self::get($url, $params, $headers, $iteration + 1);
                }

                return false;
            }

            return $response;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            if ($iteration === 1) {
                return self::get($url, $params, $headers, $iteration + 1);
            }

            return false;
        }
    }
}
