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

    public function current()
    {
        return $this->array[$this->current];
    }

    public function next()
    {
        $this->current++;
    }

    public function key()
    {
        return $this->current;
    }

    public function valid()
    {
        if (isset($this->array[$this->current])) {
            return true;
        }

        $settings = $this->reviews->getSettings();
        $offset = $settings['offset'] ?: $settings['limit'];
        if ($this->current % $settings['limit'] == 0 && $this->current > 0) {
            $this->reviews->offset(
                $settings['limit'] + $settings['offset']
            );

            $this->getMoreResults();
            return isset($this->array[$this->current]);
        }

        return false;
    }

    public function rewind()
    {
        $this->current = 0;
    }

    public function getArray()
    {
        return $this->array;
    }

    public function count()
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
