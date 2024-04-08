<?php

namespace App\Services\GO1;

use Exception;
use Illuminate\Support\Facades\Http;

class UserService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createUser($user)
    {
        try {
            $accessToken = $this->getAccessToken();
            $endPoint = "$this->endPointBaseUrl/users";
            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken",
            ])->post($endPoint, array_merge($user, ['send_login_email' => false, 'password' => config('go1.default_user_password')]));

            if ($response->status() >= 400) {
                throw new Exception("status code: {$response->status()}--{$response->body()}");
            }

            return $response->json();
        } catch (Exception $exception) {
            return false;
        }
    }
}
