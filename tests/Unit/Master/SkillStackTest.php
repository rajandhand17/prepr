<?php

namespace Tests\Unit\Models;

use App\Models\SkillStack;
use Tests\TestCase;

/**
 * Class SkillStackTest.
 *
 * @covers \App\Models\SkillStack
 */
final class SkillStackTest extends TestCase
{
    private SkillStack $skillStack;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->skillStack = new SkillStack();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->skillStack);
    }

    public function testGetSkillStacks(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
