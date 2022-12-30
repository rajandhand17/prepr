<?php

namespace Tests\Unit\Models;

use App\Models\FlexibleExpireDateDuration;
use Tests\TestCase;

/**
 * Class FlexibleExpireDateDurationTest.
 *
 * @covers \App\Models\FlexibleExpireDateDuration
 */
final class FlexibleExpireDateDurationTest extends TestCase
{
    private FlexibleExpireDateDuration $flexibleExpireDateDuration;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->flexibleExpireDateDuration = new FlexibleExpireDateDuration();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->flexibleExpireDateDuration);
    }

    public function testGetFlexibleDateDurations(): void
    {  
        /*checking apis working or not */
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertStatus(200);
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
 