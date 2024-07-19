<?php

namespace App\Helpers\Airmeet;

use App\Helpers\UtilityHelper;
use App\Models\AirmeetAuthToken;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AirmeetBaseHelper
{
    /**
     * @param string $url
     * @param array  $params
     * @param array  $headers
     * @param int    $iteration
     * @param bool   $fresh
     *
     * @return false|PromiseInterface|Response
     */
    public static function get(string $url, array $params = [], array $headers = [], int $iteration = 1, bool $fresh = false): false|PromiseInterface|Response
    {
        try {
            $airmeetHeader = self::getAirMeetHeader($fresh);
            $response = Http::baseUrl(config('airmeet.airmeet_base_url'))->withHeaders(
                [
                    ...$airmeetHeader,
                    ...$headers,
                ]
            )->withUrlParameters($params)->get($url);

            /**
             * HANDLE CLIENT ERROR.
             */
            if (!$response->ok()) {
                if ($iteration === 1) { // RETRY ONCE WITH FRESH TOKEN IN CASE OF FAILURE
                    return self::get($url, $params, $headers, $iteration + 1, true);
                }

                return false;
            }
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            /**
             * HANDLE CLIENT ERROR.
             */
            if ($iteration <= 1) { // RETRY ONCE WITH FRESH TOKEN IN CASE OF FAILURE
                return self::get($url, $params, $headers, $iteration + 1, true);
            }

            return false;
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @param int    $iteration
     * @param bool   $fresh
     *
     * @return false|PromiseInterface|Response
     */
    public static function post(string $url, array $data, array $headers = [], int $iteration = 1, bool $fresh = false): false|PromiseInterface|Response
    {
        try {
            /** DATA KEY FOR POST REQUEST */
            $airmeetHeader = self::getAirMeetHeader();
            $response = Http::baseUrl(config('airmeet.airmeet_base_url'))->withHeaders(
                [
                    ...$airmeetHeader,
                    ...$headers,
                ]
            )->post($url, $data);

            /**
             * HANDLE CLIENT ERROR.
             */
            if (!$response->ok()) {
                if ($iteration <= 1) { // RETRY ONCE WITH FRESH TOKEN IN CASE OF FAILURE
                    return self::post($url, $data, $headers, $iteration + 1, true);
                }

                return false;
            }
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            /**
             * HANDLE CLIENT ERROR.
             */
            if ($iteration <= 1) { // RETRY ONCE WITH FRESH TOKEN IN CASE OF FAILURE
                return self::post($url, $data, $headers, $iteration + 1, true);
            }

            return false;
        }

        return $response;
    }

    /**
     * @param bool $fresh
     *
     * @return array
     */
    public static function getAirMeetHeader(bool $fresh = false): array
    {
        /** @var AirmeetAuthToken|null $airmeetAuthToken */
        $airmeetAuthToken = AirmeetAuthToken::query()->first();
        if ($airmeetAuthToken) {
            if (self::isValidToken($airmeetAuthToken) && !$fresh) {
                $token = data_get($airmeetAuthToken, 'token');
            } else {
                $newToken = self::getAirMeetToken();
                $airmeetAuthToken->update(['token' => $newToken, 'expire_at' => Carbon::now()->addDays(15)]);
                $token = $newToken;
            }
        } else {
            $newToken = self::getAirMeetToken();
            AirmeetAuthToken::query()->create(['token' => $newToken, 'expire_at' => Carbon::now()->addDays(15)]);
            $token = $newToken;
        }

        return [
            'Content-Type'           => 'application/json',
            'X-Airmeet-Access-Token' => $token,
        ];
    }

    public static function getAirMeetToken()
    {
        $response = Http::baseUrl(config('airmeet.airmeet_base_url'))->withHeaders([
            'X-Airmeet-Access-Key' => config('airmeet.airmeet_access_key'),
            'X-Airmeet-Secret-Key' => config('airmeet.airmeet_secret_key'),
            'Content-Type'         => 'application/json',
        ])->post('prod/auth');

        if (!$response->ok()) {
            return false;
        }

        return data_get($response, 'token');
    }

    /**
     * @param AirmeetAuthToken $airmeetAuthToken
     *
     * @return bool
     */
    public static function isValidToken(AirmeetAuthToken $airmeetAuthToken): bool
    {
        $expiryDate = data_get($airmeetAuthToken, 'expire_at');
        if ($expiryDate) {
            $now = Carbon::now();

            return Carbon::parse($expiryDate) > $now;
        }

        return false;
    }
}
