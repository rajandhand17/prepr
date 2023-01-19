<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\CheckOrganizationRequest;
use Tests\TestCase;

/**
 * Class CheckOrganizationRequestTest.
 *
 * @covers \App\Http\Requests\Auth\CheckOrganizationRequest
 */
final class CheckOrganizationRequestTest extends TestCase
{
    private CheckOrganizationRequest $checkOrganizationRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->checkOrganizationRequest = new CheckOrganizationRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->checkOrganizationRequest);
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
