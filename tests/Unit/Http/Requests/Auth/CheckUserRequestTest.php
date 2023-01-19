<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\CheckUserRequest;
use Tests\TestCase;

/**
 * Class CheckUserRequestTest.
 *
 * @covers \App\Http\Requests\Auth\CheckUserRequest
 */
final class CheckUserRequestTest extends TestCase
{
    private CheckUserRequest $checkUserRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->checkUserRequest = new CheckUserRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->checkUserRequest);
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
