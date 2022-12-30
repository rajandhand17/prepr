<?php

namespace Tests\Unit\Models;

use App\Models\Rank;
use Tests\TestCase;

/**
 * Class RankTest.
 *
 * @covers \App\Models\Rank
 */
final class RankTest extends TestCase
{
    private Rank $rank;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->rank = new Rank();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->rank);
    }

    public function testGetRanks(): void
    {
        $response = $this->get('/api/v1/master/ranks?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
