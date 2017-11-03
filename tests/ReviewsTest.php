<?php
namespace exussum12\TripAdvisorTests;

use DateTime;
use PHPUnit\Framework\TestCase;
use exussum12\TripAdvisor\Client;
use exussum12\TripAdvisor\Reviews;
use exussum12\TripAdvisor\Response;

class ReviewsTest extends TestCase
{
    /** @var  Client|\PHPUnit_Framework_MockObject_MockObject */
    protected $client;

    /** @var  Reviews */
    protected $reviews;

    public function setUp()
    {
        parent::setUp();
        $this->client = $this->getMockBuilder(Client::class)->getMock();
        $this->reviews = new Reviews('key', 'secret', $this->client);
        $this->response = new Response(
            200,
            [],
            file_get_contents(__DIR__ . '/Fixtures/SampleResponse.json')
        );

    }

    public function testNoOptionsCase()
    {
        $signature = '2e700c366abc98c6ae771226b80e7411030f3c97382e7abcff7c8670e4cde' .
            'dd251886f45e1ce971c3de4206b7e999ec1cb18372ffbde6ea4e2f659fcf6bc73a5';
        $time = '2017-01-01T07:04:03Z';
        $response = new Response(
            200,
            [],
            '[]'
        );

        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?offset=0&limit=1000',
                'VRS-HMAC-SHA512 timestamp=' . $time . ', client=key, signature=' . $signature
            )
            ->willReturn($response);;

        $this->assertEquals([], $this->reviews->get($time)->getArray());
    }

    public function testOffsetLimit()
    {
        $signature = 'c2dad1c1f5bbd1416fff7776200d43928032291a42137650896cf3a5cff18163c9f4825f16' .
            '93725df45e1d763190551e78479977e5c746486863c86ced584e5e';

        $response = new Response(
            200,
            [],
            '[]'
        );
        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?offset=1&limit=1',
                'VRS-HMAC-SHA512 timestamp=2017-07-29T07:49:58Z, client=key, signature=' . $signature
            )
            ->willReturn($response);
        $reviews = $this->reviews->offset(1)->limit(1)->get('2017-07-29T07:49:58Z');
        $this->assertEquals([], $reviews->getArray());
    }

    public function testOnlyOneAccountRefSent()
    {
        $signature = 'ba23256699be8b6a1f3f244bb6b46ffc0a36bdf33494c3c789a6044b17b74b2021' .
        '1e80b703bb46419fd5e6c2afbbe84002e43e6b89ce9af1c0998103c30ff467';
        $response = new Response(
            200,
            [],
            '[]'
        );
        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?offset=0&limit=1000&accountReference=2345',
                'VRS-HMAC-SHA512 timestamp=2017-07-29T07:49:58Z, client=key, signature=' . $signature
            )
            ->willReturn($response);
        $reviews = $this->reviews
            ->forExternalAccount('1234')
            ->forAccount('2345')
            ->get('2017-07-29T07:49:58Z');

        $this->assertEquals([], $reviews->getArray());
    }

    public function testResponseIsParsedCorrectly()
    {
        $this->client->method('getWithSignature')
            ->with(
                $this->anything(),
                $this->anything()
            )
            ->willReturn($this->response);
        $reviews = $this->reviews->get();

        $this->assertEquals(2, count($reviews));
    }

    public function testSingleReference()
    {
        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?offset=0&limit=1000&listingReference=2345',
                $this->anything()
            )
            ->willReturn($this->response);
        $reviews = $this->reviews->forExternalReference('1234')->forListingReference("2345")->get();

        $this->assertEquals(2, count($reviews));
    }

    public function testDate()
    {
        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?offset=0&limit=1000&startDate=2017-01-01',
                $this->anything()
            )
            ->willReturn($this->response);
        $reviews = $this->reviews->since(new DateTime('2017-01-01'))->get();

        $this->assertEquals(2, count($reviews));
    }

    public function testGetSettingsBack()
    {
        $expected = [
            'startDate' => '2017-01-01',
            'offset' => 0,
            'limit' => 1000,
        ];
        $this->reviews->since(new DateTime('2017-01-01'));

        $this->assertEquals($expected, $this->reviews->getSettings());
    }

    public function testAutomaticallySelectCurl()
    {
        $this->assertInstanceOf(Reviews::class, new Reviews('key', 'secret'));
    }
}
