<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\RegisterDataRequest;
use Tests\TestCase;

/**
 * Class RegisterDataRequestTest.
 *
 * @covers \App\Http\Requests\RegisterDataRequest
 */
final class RegisterDataRequestTest extends TestCase
{
    private RegisterDataRequest $registerDataRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->registerDataRequest = new RegisterDataRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->registerDataRequest);
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
