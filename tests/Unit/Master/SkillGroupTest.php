<?php

namespace Tests\Unit\Models;

use App\Models\SkillGroup;
use Tests\TestCase;

/**
 * Class SkillGroupTest.
 *
 * @covers \App\Models\SkillGroup
 */
final class SkillGroupTest extends TestCase
{
    private SkillGroup $skillGroup;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->skillGroup = new SkillGroup();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->skillGroup);
    }

    public function testGetSkillGroups(): void
    {
        $response = $this->get('/api/v1/master/skill-groups?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
