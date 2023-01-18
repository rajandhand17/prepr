<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\RegisterRequest;
use Tests\TestCase;

/**
 * Class RegisterRequestTest.
 *
 * @covers \App\Http\Requests\RegisterRequest
 */
final class RegisterRequestTest extends TestCase
{
    private RegisterRequest $registerRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->registerRequest = new RegisterRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->registerRequest);
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
