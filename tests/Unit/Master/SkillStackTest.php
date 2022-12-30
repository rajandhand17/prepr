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
        $response = $this->get('/api/v1/master/skill-sets?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
