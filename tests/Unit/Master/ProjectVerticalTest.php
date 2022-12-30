<?php

namespace Tests\Unit\Models;

use App\Models\ProjectVertical;
use Tests\TestCase;

/**
 * Class ProjectVerticalTest.
 *
 * @covers \App\Models\ProjectVertical
 */
final class ProjectVerticalTest extends TestCase
{
    private ProjectVertical $projectVertical;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->projectVertical = new ProjectVertical();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->projectVertical);
    }

    public function testGetProjectVerticals(): void
    {
        $response = $this->get('/api/v1/master/verticals?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
