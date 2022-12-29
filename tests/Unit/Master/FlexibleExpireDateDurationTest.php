<?php

namespace Tests\Unit\Models;

use App\Models\FlexibleExpireDateDuration;
use Tests\TestCase;

/**
 * Class FlexibleExpireDateDurationTest.
 *
 * @covers \App\Models\FlexibleExpireDateDuration
 */
final class FlexibleExpireDateDurationTest extends TestCase
{
    private FlexibleExpireDateDuration $flexibleExpireDateDuration;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->flexibleExpireDateDuration = new FlexibleExpireDateDuration();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->flexibleExpireDateDuration);
    }

    public function testGetFlexibleDateDurations(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
