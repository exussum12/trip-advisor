<?php

namespace exussum12\TripAdvisor;

use Countable;
use Iterator;

class ResultSet implements Iterator, Countable
{
    protected $current = 0;
    protected $reviews;
    protected $array;

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
        if ($this->current % $offset == $this->current % $settings['limit'] && $this->current > 0) {
            $this->reviews->offset(
                $settings['limit'] + $settings['offset']
            );

            $extraResults = $this->reviews->get();
            $this->array = array_merge($this->array, $extraResults->getArray());

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
}
