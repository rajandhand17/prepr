<?php

namespace Tests\Unit\Models;

use App\Models\Host;
use Tests\TestCase;

/**
 * Class HostTest.
 *
 * @covers \App\Models\Host
 */
final class HostTest extends TestCase
{
    private Host $host;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->host = new Host();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->host);
    }

    public function testGetHosts(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
