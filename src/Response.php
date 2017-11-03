<?php
namespace exussum12\TripAdvisor;

use exussum12\TripAdvisor\Exceptions\IpDisallowed;

class Response
{
    private $body;
    private $headers;
    private $httpCode;
    private $bodyJsonStatus;

    const FORBIDDEN = 403;

    public function __construct($httpCode, $headers, $body)
    {
        $this->httpCode = $httpCode;
        $this->headers = $headers;
        $this->body = json_decode($body);
        $this->bodyJsonStatus = json_last_error();
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
        $this->checkValidJson();
        $this->checkForbidden();
    }

    protected function checkForbidden()
    {
        if ($this->httpCode == self::FORBIDDEN) {

            throw new InvalidCredentials();
        }
    }

    protected function checkValidJson()
    {
        if ($this->bodyJsonStatus !== JSON_ERROR_NONE) {
            throw new UnknownError($this->getBody());
        }
    }
}
