<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class MagnetHelper
{
    public static function updateLearningContentOnMagnet($data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('magnet.magnet_auth_token'),
            ])->post(config('magnet.update_learning_content'), $data);

            if (!$response->ok()) {
                return false;
            }

            return $response->json();
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function getMagnetUser($token)
    {
        try {
            //Get magnet user info
            $userResponse = self::getMagnetUserInfo($token);

            if (!$userResponse) {
                return false;
            }

            return $userResponse;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getTokenFromMagnet($authorizationCode)
    {
        try {
            $frontendUrl = UtilityHelper::sanitizeUrl(config('site-settings.frontend_site_url'));
            $params = [
                'grant_type' => 'authorization_code',
                'client_id' => config('magnet.client_id'),
                'redirect_uri' => $frontendUrl . '/magnet/callback',
                'client_secret' => config('magnet.client_secret'),
                'code' => $authorizationCode,
            ];

            $response = Http::withHeaders(
                [
                    'User-Agent' => config('magnet.user_agent'),
                ]
            )->post(config('magnet.magnet_oauth_token'), $params);

            if (!$response->ok()) {
                return false;
            }

            $accessToken = data_get($response->json(), 'access_token');
            if (!$accessToken) {
                return false;
            }

            return $accessToken;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function getMagnetUserInfo($accessToken)
    {
        try {
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $accessToken])->get(config('magnet.magnet_oauth_get_user'));
            if (!$response->ok()) {
                return false;
            }

            return $response->json();
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function getLMSUserInfo($accessToken)
    {
        try {
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $accessToken])->get(config('magnet.magnet_lms_user_info'));
            if (!$response->ok()) {
                return false;
            }

            return $response->json();
        } catch (\Exception $exception) {
            return false;
        }
    }
}
