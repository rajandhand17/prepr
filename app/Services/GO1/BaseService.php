<?php

namespace App\Services\GO1;

use App\Models\GO1AccessToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BaseService
{
    private $baseUrl = 'https://api.go1.com';

    private $version = 'v2';

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $endPointBaseUrl;

    protected $auth;

    public function __construct()
    {
        $this->endPointBaseUrl = $this->baseUrl.'/'.$this->version;
        $this->auth = new AuthenticationService();
        $this->accessToken = $this->getAccessToken();
    }

    private function isTokenExpired($token): bool
    {
        if (!$token) {
            return true;
        }

        $payload = explode('.', $token)[1];
        $decodedPayload = base64_decode($payload);

        return time() > json_decode($decodedPayload)->exp;
    }

    protected function getAccessToken(): string
    {
        try {
            $data = GO1AccessToken::query()->first();

            if (!$data) {
                $responseData = $this->auth->fetchToken();
                GO1AccessToken::query()->create([
                    'access_token' => $responseData['access_token'],
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ]);

                return $responseData['access_token'];
            }

            $existingToken = $data->access_token ?? null;

            if ($this->isTokenExpired($existingToken)) {
                $responseData = $this->auth->fetchToken();

                if (!$responseData) {
                    return false;
                }

                GO1AccessToken::query()->where('id', $data->id)->update([
                    'access_token' => $responseData['access_token'],
                ]);

                return $responseData['access_token'];
            }

            return $existingToken;
        } catch (\Exception $exception) {
            Log::error($exception);

            return false;
        }
    }
}
