<?php

namespace Tests\Unit\Http\Requests\Master;

use App\Http\Requests\Master\SmsRequest;
use Tests\TestCase;

/**
 * Class SmsRequestTest.
 *
 * @covers \App\Http\Requests\Master\SmsRequest
 */
final class SmsRequestTest extends TestCase
{
    private SmsRequest $smsRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->smsRequest = new SmsRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->smsRequest);
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
