<?php

namespace Tests\Unit\Models;

use App\Models\LabCondition;
use Tests\TestCase;

/**
 * Class LabConditionTest.
 *
 * @covers \App\Models\LabCondition
 */
final class LabConditionTest extends TestCase
{
    private LabCondition $labCondition;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->labCondition = new LabCondition();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->labCondition);
    }

    public function testGetLabConditions(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
