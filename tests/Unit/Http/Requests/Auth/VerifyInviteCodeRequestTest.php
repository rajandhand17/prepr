<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\VerifyInviteCodeRequest;
use Tests\TestCase;

/**
 * Class VerifyInviteCodeRequestTest.
 *
 * @covers \App\Http\Requests\Auth\VerifyInviteCodeRequest
 */
final class VerifyInviteCodeRequestTest extends TestCase
{
    private VerifyInviteCodeRequest $verifyInviteCodeRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->verifyInviteCodeRequest = new VerifyInviteCodeRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->verifyInviteCodeRequest);
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
