<?php

namespace exussum12\TripAdvisor;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

class Reviews
{
    const URL = "https://rentals.tripadvisor.com/api/reviews/v1";
    private $clientCode;
    private $secret;
    private $client;
    private $urlArgs = [
        'offset' => 0,
        'limit' => 1000,
    ];
    public function __construct($clientCode, $secret, Client $client)
    {
        $this->clientCode = $clientCode;
        $this->secret = $secret;
        $this->client = $client;
    }

    public function since(DateTimeInterface $date)
    {
        $this->urlArgs['startDate'] = $date->format("Y-m-d");

        return $this;
    }

    public function forAccount($account)
    {
        unset($this->urlArgs['externalAccountReference']);
        $this->urlArgs['accountReference'] = $account;

        return $this;
    }

    public function forExternalAccount($account)
    {
        unset($this->urlArgs['accountReference']);
        $this->urlArgs['externalAccountReference'] = $account;

        return $this;
    }

    public function forListingReference($account)
    {
        unset($this->urlArgs['externalListingReference']);
        $this->urlArgs['listingReference'] = $account;

        return $this;
    }

    public function forExternalReference($reference)
    {
        unset($this->urlArgs['listingReference']);
        $this->urlArgs['externalListingReference'] = $reference;

        return $this;

    }

    public function offset($offset)
    {
        $this->urlArgs['offset'] = $offset;

        return $this;
    }

    public function limit($limit)
    {
        $this->urlArgs['limit'] = $limit;

        return $this;
    }

    public function get($time = null)
    {
        $url = parse_url(static::URL);
        $args = http_build_query($this->urlArgs);

        $time = new DateTime($time);
        $time->setTimezone(new DateTimeZone('UTC'));
        $time = $time->format('Y-m-d\TH:i:s\Z');

        $request = [
            'GET',
            $url['path'],
            $args,
            $time,
            hash('sha512', ''),
        ];

        $hashedRequest = hash('sha512', implode("\n", $request));

        $signature = hash_hmac('sha512', $hashedRequest, $this->secret);

        $authHeader =
            "VRS-HMAC-SHA512 " .
            "timestamp=$time, " .
            "client={$this->clientCode}, " .
            "signature=$signature";

        $url = static::URL;
        if (strlen($args) > 0) {
            $url .= '?' . $args;
        }

        return $this->makeReviews(
            $this->client->getWithSignature($url, $authHeader)
        );
    }

    public function getSettings()
    {
        return $this->urlArgs;
    }

    private function makeReviews($response)
    {
        $reviews = json_decode($response);
        $out = [];
        if (isset($reviews->reviews)) {
            foreach ($reviews->reviews as $review) {
                $out[] = new Review($review);
            }
        }

        return new ResultSet($this, $out);
    }

}