<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\submitResetPasswordFormRequest;
use Tests\TestCase;

/**
 * Class submitResetPasswordFormRequestTest.
 *
 * @covers \App\Http\Requests\Auth\submitResetPasswordFormRequest
 */
final class submitResetPasswordFormRequestTest extends TestCase
{
    private submitResetPasswordFormRequest $submitResetPasswordFormRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->submitResetPasswordFormRequest = new submitResetPasswordFormRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->submitResetPasswordFormRequest);
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
