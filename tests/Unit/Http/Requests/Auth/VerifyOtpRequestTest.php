<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\VerifyOtpRequest;
use Tests\TestCase;

/**
 * Class VerifyOtpRequestTest.
 *
 * @covers \App\Http\Requests\Auth\VerifyOtpRequest
 */
final class VerifyOtpRequestTest extends TestCase
{
    private VerifyOtpRequest $verifyOtpRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->verifyOtpRequest = new VerifyOtpRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->verifyOtpRequest);
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
