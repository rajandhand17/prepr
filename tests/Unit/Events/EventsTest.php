<?php

namespace Tests\Unit\Events;

use App\Events\Events;
use Tests\TestCase;

/**
 * Class EventsTest.
 *
 * @covers \App\Events\Events
 */
final class EventsTest extends TestCase
{
    private Events $events;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->events = new Events();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->events);
    }

    public function testBroadcastOn(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
