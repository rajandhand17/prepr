<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\FormDataRequest;
use Tests\TestCase;

/**
 * Class FormDataRequestTest.
 *
 * @covers \App\Http\Requests\FormDataRequest
 */
final class FormDataRequestTest extends TestCase
{
    private FormDataRequest $formDataRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->formDataRequest = new FormDataRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->formDataRequest);
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
