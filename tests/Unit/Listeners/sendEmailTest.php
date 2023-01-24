<?php

namespace Tests\Unit\Listeners;

use App\Events\Events;
use App\Listeners\sendEmail;
use Mockery;
use Tests\TestCase;

/**
 * Class sendEmailTest.
 *
 * @covers \App\Listeners\sendEmail
 */
final class sendEmailTest extends TestCase
{
    private sendEmail $sendEmail;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sendEmail = new sendEmail();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->sendEmail);
    }

    public function testHandle(): void
    {
        $event = Mockery::mock(Events::class);

        /** @todo This test is incomplete. */
        $this->sendEmail->handle($event);
    }
}
