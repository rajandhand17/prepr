<?php

namespace App\Traits;

use App\Models\User;

trait MagnetTrait
{
    /* -----------------------------------------------------------------------------------------
      @Description: Function for getting recommendations
      @output: returns an array of recommended labs, resources, challenges and projects.
      -------------------------------------------------------------------------------------------- */



    private function handleMagnetResponse($request)
    {
        try {
            $authorizationCode = $request->code;

            //Get token from magnet method call
            $response = $this->getTokenFromMagnet($authorizationCode);

            if ($response->status == 'error') {
                return [
                    'message' => $response->message,
                    'status' => 'error',
                ];
            } else {
                $access_token = $response->data->access_token;

                //Get magnet user info
                $userResponse = $this->getMagnetUserInfo($access_token);

                if ($userResponse->status == 'error') {
                    return [
                        'message' => $userResponse->message,
                        'status' => 'error'
                    ];
                } else {
                    return [
                        'data' => $userResponse->data,
                        'access_token' => $access_token,
                        'status' => 'success'
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'message' => 'Something went wrong.',
                'status' => 'error'
            ];
        }
    }

    //Generate Oauth TOken from magnet
    private function getTokenFromMagnet($authorizationCode)
    {
        //Required Params
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => config('magnet.client_id'),
            'redirect_uri' => url('/') . '/login/magnet/callback',
            'client_secret' => config('magnet.client_secret'),
            'code' => $authorizationCode,
        ];
        //Curl Call to magnet api with Params
        $curlOpts = [
            CURLOPT_USERAGENT => config('magnet.user_agent'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_URL => config('magnet.magnet_oauth_token'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
                // CURLOPT_VERBOSE        => true,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $curlOpts);
        $curl_data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_status == 200) {
            return (object) [
                        'data' => json_decode($curl_data),
                        'status' => 'success'
            ];
        } else {
            return (object) [
                        'message' => "Token Call failed: " . curl_error($ch),
                        'status' => 'error'
            ];
        }
    }

    //Get User info from magnet
    private function getMagnetUserInfo($access_token)
    {
        $curl = curl_init();
        //curl call to get magnet user info
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('magnet.magnet_oauth_get_user'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . (string) $access_token
            ),
        ));
        $response = curl_exec($curl);

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($http_status == 200) {
            return (object) [
                        'data' => json_decode($response),
                        'status' => 'success'
            ];
        } else {
            $response=json_decode($response);
            return (object) [
                        'message' => "Get Basic User Info Call failed: " . isset($response->message) ? $response->message : '',
                        'status' => 'error'
            ];
        }
    }

}
