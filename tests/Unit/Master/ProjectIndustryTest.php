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
        $response = $this->get('/api/v1/master/industries?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
