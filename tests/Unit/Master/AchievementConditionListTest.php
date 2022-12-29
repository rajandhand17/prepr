<?php

namespace Tests\Unit\Models;

use App\Models\AchievementConditionList;
use Tests\TestCase;

/**
 * Class AchievementConditionListTest.
 *
 * @covers \App\Models\AchievementConditionList
 */
final class AchievementConditionListTest extends TestCase
{
    private AchievementConditionList $achievementConditionList;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->achievementConditionList = new AchievementConditionList();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->achievementConditionList);
    }

    public function testGetAchievementConditionLists(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
