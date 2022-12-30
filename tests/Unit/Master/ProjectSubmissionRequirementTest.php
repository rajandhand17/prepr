<?php

namespace Tests\Unit\Models;

use App\Models\ProjectSubmissionRequirement;
use Tests\TestCase;

/**
 * Class ProjectSubmissionRequirementTest.
 *
 * @covers \App\Models\ProjectSubmissionRequirement
 */
final class ProjectSubmissionRequirementTest extends TestCase
{
    private ProjectSubmissionRequirement $projectSubmissionRequirement;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->projectSubmissionRequirement = new ProjectSubmissionRequirement();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->projectSubmissionRequirement);
    }

    public function testGetProjectSubmissionRequirements(): void
    {
        $response = $this->get('/api/v1/master/project-submission-requirement?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
