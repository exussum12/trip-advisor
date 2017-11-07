<?php
namespace exussum12\TripAdvisor\Clients;

use exussum12\TripAdvisor\Client;
use exussum12\TripAdvisor\Response;

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
            function ($curl, $header) use (&$headers) {
                if (strpos($header, ':') !== false) {
                    list($key, $value) = explode(':', $header, 2);
                    $headers[$key] = trim($value);
                }

                return strlen($header);
            }
        );

        $body = curl_exec($this->curlHandle);
        $statusCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);

        return new Response($statusCode, $headers, $body);
    }
}
