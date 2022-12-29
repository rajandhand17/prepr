<?php

namespace Tests\Unit\Models;

use App\Models\ProjectStatus;
use Tests\TestCase;

/**
 * Class ProjectStatusTest.
 *
 * @covers \App\Models\ProjectStatus
 */
final class ProjectStatusTest extends TestCase
{
    private ProjectStatus $projectStatus;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->projectStatus = new ProjectStatus();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->projectStatus);
    }

    public function testGetProjectStatus(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
