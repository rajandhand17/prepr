<?php

namespace App\Services\Manage;

use App\Models\GO1AccessToken;
use Carbon\Carbon;
use Exception;

class GO1AccessTokenService
{
    private $baseUrl = 'https://api.go1.com';

    private $version = 'v2';

    /**
     * @var string
     */
    public $endPointBaseUrl;

    protected $auth;

    public function __construct()
    {
        $this->endPointBaseUrl = $this->baseUrl . '/' . $this->version;
    }

    public function getAccessToken()
    {
        try {
            return GO1AccessToken::query()->first();
        } catch (Exception $exception) {
            return false;
        }
    }

    public function createAccessToken($token)
    {
        try {
            return GO1AccessToken::query()->create([
                'access_token' => $token,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function updateAccessToken($token, $id)
    {
        try {
            return GO1AccessToken::query()->where('id', $id)->update([
                'access_token' => $token,
            ]);
        } catch (Exception $exception) {
            return false;
        }
    }
}
