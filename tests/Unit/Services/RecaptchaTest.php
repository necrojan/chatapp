<?php

namespace Tests\Unit\Services;

use App\Services\Recaptcha;
use Mockery;
use Tests\TestCase;

class RecaptchaTest extends TestCase
{
    /** @test */
    public function it_returns_an_array_response()
    {
        $captcha = Mockery::mock(Recaptcha::class);

        $captcha->shouldReceive('sumbmit')
            ->with(Mockery::type('array'))
            ->andReturn([]);
    }
}
