<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\SendOtpRequest;
use Tests\TestCase;

/**
 * Class SendOtpRequestTest.
 *
 * @covers \App\Http\Requests\Auth\SendOtpRequest
 */
final class SendOtpRequestTest extends TestCase
{
    private SendOtpRequest $sendOtpRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->sendOtpRequest = new SendOtpRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->sendOtpRequest);
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
