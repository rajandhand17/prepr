<?php

namespace Tests\Unit\Models;

use App\Models\ProjectType;
use Tests\TestCase;

/**
 * Class ProjectTypeTest.
 *
 * @covers \App\Models\ProjectType
 */
final class ProjectTypeTest extends TestCase
{
    private ProjectType $projectType;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->projectType = new ProjectType();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->projectType);
    }

    public function testGetProjectTypes(): void
    {
        $response = $this->get('/api/v1/master/types?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
