<?php

namespace Tests\Unit\Models;

use App\Models\Skill;
use Tests\TestCase;

/**
 * Class SkillTest.
 *
 * @covers \App\Models\Skill
 */
final class SkillTest extends TestCase
{
    private Skill $skill;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->skill = new Skill();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->skill);
    }

    public function testGetSkills(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
