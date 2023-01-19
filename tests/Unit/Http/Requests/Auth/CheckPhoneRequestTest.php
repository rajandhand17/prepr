<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\CheckPhoneRequest;
use Tests\TestCase;

/**
 * Class CheckPhoneRequestTest.
 *
 * @covers \App\Http\Requests\Auth\CheckPhoneRequest
 */
final class CheckPhoneRequestTest extends TestCase
{
    private CheckPhoneRequest $checkPhoneRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->checkPhoneRequest = new CheckPhoneRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->checkPhoneRequest);
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
