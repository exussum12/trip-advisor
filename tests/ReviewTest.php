<?php
namespace exussum12\TripAdvisorTests;

use exussum12\TripAdvisor\Exceptions\ImmutableObjectException;
use PHPUnit\Framework\TestCase;
use exussum12\TripAdvisor\Review;

class ReviewTest extends TestCase
{
    public function testSetMethodThrowsException()
    {
        $this->expectException(ImmutableObjectException::class);
        $review = new Review([]);
        $review->test = "1234";
    }
}
