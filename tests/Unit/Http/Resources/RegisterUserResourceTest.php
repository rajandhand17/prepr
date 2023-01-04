<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\RegisterUserResource;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class RegisterUserResourceTest.
 *
 * @covers \App\Http\Resources\RegisterUserResource
 */
final class RegisterUserResourceTest extends TestCase
{
    private RegisterUserResource $registerUserResource;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->registerUserResource = new RegisterUserResource();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->registerUserResource);
    }

    public function testToArray(): void
    {
        $request = Mockery::mock(Request::class);

        /** @todo This test is incomplete. */
        $this->assertSame([], $this->registerUserResource->toArray($request));
    }
}
