<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\RegisterResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class RegisterResourceTest.
 *
 * @covers \App\Http\Resources\RegisterResource
 */
final class RegisterResourceTest extends TestCase
{
    private RegisterResource $registerResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->registerResource = new RegisterResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->registerResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        $this->assertSame([], $this->registerResource->toArray($request));
    }
}
