<?php

namespace Tests\Unit\Models;

use App\Models\ProjectIndustry;
use Tests\TestCase;

/**
 * Class ProjectIndustryTest.
 *
 * @covers \App\Models\ProjectIndustry
 */
final class ProjectIndustryTest extends TestCase
{
    private ProjectIndustry $projectIndustry;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->projectIndustry = new ProjectIndustry();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->projectIndustry);
    }

    public function testGetProjectIndustries(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
