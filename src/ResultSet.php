<?php

namespace exussum12\TripAdvisor;

use Iterator;

class ResultSet implements Iterator
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
        if ($this->current % $settings['limit'] == 0 && $this->current > 0) {
            $this->reviews->offset(
                $settings['limit'] + $settings['offset']
            );
            var_dump("set offset to " , ($settings['limit'] + $settings['offset']));

            $extraResults = $this->reviews->get();
            $this->array = array_merge($this->array,$extraResults->get());
            var_dump("Adding");
            var_dump(count($this->array));
            sleep(5);
            var_dump(count($this->array));

            return isset($this->array[$this->current]);
        }

        return false;
    }

    public function rewind()
    {
        $this->current = 0;
    }

    public function get()
    {
        return $this->array;
    }
}
