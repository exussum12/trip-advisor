<?php
namespace exussum12\TripAdvisorTests;

use exussum12\TripAdvisor\Exceptions\InvalidCredentials;
use PHPUnit\Framework\TestCase;
use exussum12\TripAdvisor\Exceptions\RateLimit;
use exussum12\TripAdvisor\ResultSet;
use exussum12\TripAdvisor\Reviews;

class ResultSetTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testInitialLoad()
    {
        $review = $this->createMock(Reviews::class);

        $resultSet = new ResultSetTest($review, []);

        $this->assertInstanceOf(ResultSetTest::class, $resultSet);
    }

    public function testCountable()
    {
        
        $review = $this->createMock(Reviews::class);

        $resultSet = new ResultSet($review, [1,2]);

        $this->assertEquals(2, count($resultSet));
    }

    public function testIterating()
    {
        
        $review = $this->createMock(Reviews::class);

        $resultSet = new ResultSet($review, [1,2]);
        $extraResults = new ResultSet($review, [3]);
        $lastResult = new ResultSet($review, []);

        $review->method('getSettings')->willReturn(['offset' => 0, 'limit' => 2]);
        $review->method('offset')->with(2);
        $review->method('get')->will($this->onConsecutiveCalls($extraResults, $lastResult));

        $count = 0;
        //Just getting the count when iterating
        foreach ($resultSet as $key => $result) {
            $count++;
        }

        $this->assertEquals(3, $count);
        $this->assertEquals(2, $key);
    }

    public function testIteratingiWithNonNormalOffset()
    {
        
        $review = $this->createMock(Reviews::class);

        $resultSet = new ResultSet($review, [1,2]);
        $extraResults = new ResultSet($review, [3]);
        $lastResult = new ResultSet($review, []);

        $review->method('getSettings')->willReturn(['offset' => 1, 'limit' => 2]);
        $review->method('offset')->with(3);
        $review->method('get')->will($this->onConsecutiveCalls($extraResults, $lastResult));

        $count = 0;
        foreach ($resultSet as $key => $result) {
            $count++;
        }

        $this->assertEquals(3, $count);
        $this->assertEquals(2, $key);
    }

    public function testRateLimitedReply()
    {
        $review = $this->createMock(Reviews::class);
        $review
            ->expects($this->exactly(5))
            ->method('get')
            ->willThrowException(new RateLimit());


        $resultSet = new ResultSet($review, [1,2]);
        $review->method('getSettings')->willReturn(['offset' => 1, 'limit' => 2]);
        $review->method('offset')->with(3);

        $count = 0;
        foreach ($resultSet as $key => $result) {
            $count++;
        }

        $this->assertEquals(2, $count);
        $this->assertEquals(1, $key);
    }

    public function testOtherError()
    {
        $this->expectException(InvalidCredentials::class);
        $review = $this->createMock(Reviews::class);
        $review
            ->method('get')
            ->willThrowException(new InvalidCredentials());


        $resultSet = new ResultSet($review, [1,2]);
        $review->method('getSettings')->willReturn(['offset' => 1, 'limit' => 2]);
        $review->method('offset')->with(3);

        $count = 0;
        foreach ($resultSet as $key => $result) {
            $count++;
        }

    }
}
