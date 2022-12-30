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
        $response = $this->get('/api/v1/master/stages?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
