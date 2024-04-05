<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class BaseTestCase extends TestCase
{
    use DatabaseTransactions;

    protected $parameters;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->parameters = [
            'email'    => 'testprepradmin@gmail.com',
            'password' => 'Test@1234',
        ];
        Auth::attempt(['email' => $this->parameters['email'], 'password' => $this->parameters['password']]);
        $user = Auth::user();
        $this->actingAs($user, 'api');
    }
}
