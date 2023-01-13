<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\LoginDataRequest;
use Tests\TestCase;

/**
 * Class LoginDataRequestTest.
 *
 * @covers \App\Http\Requests\LoginDataRequest
 */
final class LoginDataRequestTest extends TestCase
{
    private LoginDataRequest $loginDataRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->loginDataRequest = new LoginDataRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->loginDataRequest);
    }

    public function testAuthorize(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRules(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
