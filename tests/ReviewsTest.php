<?php
namespace exussum12\TripAdvisorTests;

use DateTime;
use exussum12\TripAdvisor\Client;
use PHPUnit\Framework\TestCase;
use exussum12\TripAdvisor\Reviews;

class ReviewsTest extends TestCase
{

    protected $client;

    /** @var  Reviews */
    protected $reviews;

    public function setUp()
    {
        parent::setUp();
        $this->client = $this->getMockBuilder(Client::class)->getMock();
        $this->reviews = new Reviews('key', 'secret', $this->client);
    }

    public function testNoOptionsCase()
    {
        $this->client->method('getWithSignature')
            ->with(Reviews::URL, 'VRS-HMAC-SHA512 timestamp=2017-01-01T07:04:03Z, client=key, signature=94eee14544c1f4f50675b2ab2948ba71ac4c314185dd47c694853f2e9929889a35bc27a39c49fcc9782aeea40ec52ae4558851024d21557857a53096bfb23b81')
            ->willReturn('[]');

        $this->assertEquals([],$this->reviews->get('2017-01-01T07:04:03Z'));
    }

    public function testOffsetLimit()
    {
        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?offset=1&limit=1',
                'VRS-HMAC-SHA512 timestamp=2017-07-29T07:49:58Z, client=key, signature=c2dad1c1f5bbd1416fff7776200d43928032291a42137650896cf3a5cff18163c9f4825f1693725df45e1d763190551e78479977e5c746486863c86ced584e5e'
            )
            ->willReturn('[]');
        $reviews = $this->reviews->offset(1)->limit(1)->get('2017-07-29T07:49:58Z');
        $this->assertEquals([],$reviews);

    }

    public function testOnlyOneAccountRefSent()
    {
        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?accountReference=2345',
                'VRS-HMAC-SHA512 timestamp=2017-07-29T07:49:58Z, client=key, signature=d297b95b875cf9318f7b1efaa4744ee207d9d759c2061b310a830efbd42d4afb9bdca0eb56ef2c2fe388e24dd9d2eae35bd1dd2ac2f92c63408a8622df3c9f69'
            )
            ->willReturn('[]');
        $reviews = $this->reviews->forExternalAccount('1234')->forAccount('2345')->get('2017-07-29T07:49:58Z');
        $this->assertEquals([],$reviews);

    }

    public function testResponseIsParsedCorrectly()
    {
        $this->client->method('getWithSignature')
            ->with(
                $this->anything(),
                $this->anything()
            )
            ->willReturn(file_get_contents(__DIR__ . '/Fixtures/SampleResponse.json'));
        $reviews = $this->reviews->get();
        $this->assertEquals(2,count($reviews));
    }

    public function testSingleReference()
    {
        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?listingReference=2345',
                $this->anything()
            )
            ->willReturn(file_get_contents(__DIR__ . '/Fixtures/SampleResponse.json'));
        $reviews = $this->reviews->forExternalReference('1234')->forListingReference("2345")->get();
        $this->assertEquals(2,count($reviews));
    }
    public function testDate()
    {
        $this->client->method('getWithSignature')
            ->with(
                Reviews::URL . '?startDate=2017-01-01',
                $this->anything()
            )
            ->willReturn(file_get_contents(__DIR__ . '/Fixtures/SampleResponse.json'));
        $reviews = $this->reviews->since(new DateTime('2017-01-01'))->get();
        $this->assertEquals(2,count($reviews));
    }
}