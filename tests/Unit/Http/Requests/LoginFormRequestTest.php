<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\LoginFormRequest;
use Tests\TestCase;

/**
 * Class LoginFormRequestTest.
 *
 * @covers \App\Http\Requests\LoginFormRequest
 */
final class LoginFormRequestTest extends TestCase
{
    private LoginFormRequest $loginFormRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->loginFormRequest = new LoginFormRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->loginFormRequest);
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
