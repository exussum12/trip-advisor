<?php
namespace exussum12\TripAdvisor\Exceptions;

class RateLimit extends BaseException
{
    private $timeout = 60;

    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }
}
