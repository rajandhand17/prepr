<?php

namespace Tests\Unit\Models;

use App\Models\ProjectType;
use Tests\TestCase;

/**
 * Class ProjectTypeTest.
 *
 * @covers \App\Models\ProjectType
 */
final class ProjectTypeTest extends TestCase
{
    private ProjectType $projectType;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->projectType = new ProjectType();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->projectType);
    }

    public function testGetProjectTypes(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
