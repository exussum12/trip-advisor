<?php
namespace exussum12\TripAdvisor\Clients;

use exussum12\TripAdvisor\Client;

class Curl implements Client
{
    private $curlHandle;
    public function __construct()
    {
        $this->curlHandle = curl_init();
        curl_setopt(
            $this->curlHandle,
            CURLOPT_USERAGENT,
            'Exussum Tripadvisor Client'
        );
    }
    public function getWithSignature($url, $signature)
    {
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt(
            $this->curlHandle,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: ' . $signature,
            ]
        );
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($this->curlHandle, CURLOPT_VERBOSE, 1);
        //curl_setopt($this->curlHandle, CURLOPT_HEADER, 1);
        return curl_exec($this->curlHandle);
    }
}
