<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\CheckEmailRequest;
use Tests\TestCase;

/**
 * Class CheckEmailRequestTest.
 *
 * @covers \App\Http\Requests\Auth\CheckEmailRequest
 */
final class CheckEmailRequestTest extends TestCase
{
    private CheckEmailRequest $checkEmailRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->checkEmailRequest = new CheckEmailRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->checkEmailRequest);
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
