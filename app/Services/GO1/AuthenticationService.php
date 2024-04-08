<?php

namespace App\Services\GO1;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthenticationService
{
    private $authBaseUrl = 'https://auth.go1.com';

    public function fetchToken()
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("$this->authBaseUrl/oauth/token", [
                'client_id'     => config('go1.client_id'),
                'client_secret' => config('go1.client_secret'),
                'grant_type'    => 'client_credentials',
            ]);

            if (!$response->ok()) {
                throw new Exception("GO1 Errored Occurred with {$response->status()} code. {$response->body()}");
            }

            return $response->json();
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }
}
