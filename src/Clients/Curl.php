<?php
namespace exussum12\TripAdvisor\Clients;

use exussum12\TripAdvisor\Client;

class Curl implements Client
{
    private $curlHandle;
    public function __construct($curl = null)
    {
        if (is_null($curl)) {
            $curl = curl_init();
        }
        $this->curlHandle = $curl;
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
        $headers = [];
        curl_setopt(
            $this->curlHandle,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use ($headers) {
                list($key, $value) = explode($header, ':', 2);
            }
        );

        return curl_exec($this->curlHandle);
    }
}
