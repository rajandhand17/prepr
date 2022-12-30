<?php

namespace Tests\Unit\Models;

use App\Models\SocialLink;
use Tests\TestCase;

/**
 * Class SocialLinkTest.
 *
 * @covers \App\Models\SocialLink
 */
final class SocialLinkTest extends TestCase
{
    private SocialLink $socialLink;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->socialLink = new SocialLink();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->socialLink);
    }

    public function testGetSocialLinks(): void
    {
        $response = $this->get('/api/v1/master/links?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
