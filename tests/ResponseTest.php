<?php

use exussum12\TripAdvisor\Exceptions\InvalidCredentials;
use exussum12\TripAdvisor\Exceptions\RateLimit;
use exussum12\TripAdvisor\Exceptions\UnknownError;
use exussum12\TripAdvisor\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testCanReadInformation()
    {
        $response = new Response(200, [], '[]');

        $this->assertEquals(200, $response->getHttpCode());
        $this->assertEquals([], $response->getHeaders());
        $this->assertEquals([], $response->getBody());
    }

    public function testInvalidCredentials()
    {
        $this->expectException(InvalidCredentials::class);
        new Response(403, [], '[]');
    }

    public function testWrongJson()
    {
        $this->expectException(UnknownError::class);
        new Response(403, [], 'InvalidJson');
    }

    public function testRateLimit()
    {
        $this->expectException(RateLimit::class);
        new Response(429, ['Retry-After' => 30], '');
    }
}
