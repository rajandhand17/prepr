<?php

namespace Tests\Unit\Models;

use App\Models\ProjectStage;
use Tests\TestCase;

/**
 * Class ProjectStageTest.
 *
 * @covers \App\Models\ProjectStage
 */
final class ProjectStageTest extends TestCase
{
    private ProjectStage $projectStage;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->projectStage = new ProjectStage();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->projectStage);
    }

    public function testGetProjectStages(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
