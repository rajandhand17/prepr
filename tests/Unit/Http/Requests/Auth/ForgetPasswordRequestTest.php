<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\ForgetPasswordRequest;
use Tests\TestCase;

/**
 * Class ForgetPasswordRequestTest.
 *
 * @covers \App\Http\Requests\Auth\ForgetPasswordRequest
 */
final class ForgetPasswordRequestTest extends TestCase
{
    private ForgetPasswordRequest $forgetPasswordRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->forgetPasswordRequest = new ForgetPasswordRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->forgetPasswordRequest);
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
