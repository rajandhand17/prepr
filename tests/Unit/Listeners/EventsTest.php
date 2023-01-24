<?php

namespace Tests\Unit\Listeners;

use App\Listeners\Events;
use Tests\TestCase;

/**
 * Class EventsTest.
 *
 * @covers \App\Listeners\Events
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

    public function testHandle(): void
    {
        $event = new \stdClass();

        /** @todo This test is incomplete. */
        $this->events->handle($event);
    }
}
