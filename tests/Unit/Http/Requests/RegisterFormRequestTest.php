<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\RegisterFormRequest;
use Tests\TestCase;

/**
 * Class RegisterFormRequestTest.
 *
 * @covers \App\Http\Requests\RegisterFormRequest
 */
final class RegisterFormRequestTest extends TestCase
{
    private RegisterFormRequest $registerFormRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->registerFormRequest = new RegisterFormRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->registerFormRequest);
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
