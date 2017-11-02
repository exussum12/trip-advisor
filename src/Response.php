<?php
namespace exussum12\TripAdvisor;

use exussum12\TripAdvisor\Exceptions\IpDisallowed;

class Response
{
    private $body;
    private $headers;
    private $httpCode;

    const FORBIDDEN = 403;

    public function __construct($httpCode, $headers, $body)
    {
        $this->httpCode = $httpCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->handleResponse();
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    protected function handleResponse()
    {
        $this->checkForbidden();
    }

    protected function checkForbidden()
    {
        if ($this->httpCode == self::FORBIDDEN) {
            $body = json_decode($this->getBody());

            if (json_last_error() == JSON_ERROR_NONE) {
                throw new UnknownError($body);
            }
            throw new IpDisallowed();
        }
    }
}
