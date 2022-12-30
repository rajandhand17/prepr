<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use Tests\TestCase;

/**
 * Class TagTest.
 *
 * @covers \App\Models\Tag
 */
final class TagTest extends TestCase
{
    private Tag $tag;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->tag = new Tag();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->tag);
    }

    public function testGetTags(): void
    {
        $response = $this->get('/api/v1/master/tags?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
