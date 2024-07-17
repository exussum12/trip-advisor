<?php

namespace exussum12\TripAdvisor;

use Countable;
use exussum12\TripAdvisor\Exceptions\BaseException;
use exussum12\TripAdvisor\Exceptions\RateLimit;
use Iterator;

class ResultSet implements Iterator, Countable
{
    protected $array;
    protected $current = 0;
    protected $rateLimitRetryAttempts = 5;
    protected $rateLimitTimeout = 10;
    protected $reviews;

    public function __construct(Reviews $reviews, array $startingArray)
    {
        $this->array = $startingArray;
        $this->reviews = $reviews;
    }

    public function current(): mixed
    {
        return $this->array[$this->current];
    }

    public function next(): void
    {
        $this->current++;
    }

    public function key(): mixed
    {
        return $this->current;
    }

    public function valid(): bool
    {
        if (isset($this->array[$this->current])) {
            return true;
        }

        $settings = $this->reviews->getSettings();
        if ($this->current % $settings['limit'] == 0 && $this->current > 0) {
            $this->reviews->offset(
                $settings['limit'] + $settings['offset']
            );

            $this->getMoreResults();
            return isset($this->array[$this->current]);
        }

        return false;
    }

    public function rewind(): void
    {
        $this->current = 0;
    }

    public function getArray(): array
    {
        return $this->array;
    }

    public function count():int
    {
        return count($this->array);
    }

    protected function getMoreResults()
    {
        $i = 0;
        while ($i++ < $this->rateLimitRetryAttempts) {
            try {
                $extraResults = $this->reviews->get();
                $this->array = array_merge($this->array, $extraResults->getArray());
                break;
            } catch (RateLimit $exception) {
                sleep($exception->getTimeout());
                continue;
            } catch (BaseException $exception) {
                throw $exception;
            }
        }
    }
}
