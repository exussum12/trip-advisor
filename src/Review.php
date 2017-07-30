<?php
namespace exussum12\TripAdvisor;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * @property string reviewReference
 * @property string listingReference
 * @property string externalListingReference
 * @property bool isDeleted
 * @property DateTimeInterface reviewDate
 * @property DateTimeInterface|null visitDate
 * @property string title
 * @property string reviewLanguage
 * @property string managementResponse
 * @property string responseLanguage
 * @property double rating
 * @property string authorName
 * @property string authorLocation
 * @property string accountReference
 * @property string externalAccountReference
 */
class Review
{
    protected $review;
    public function __construct($review)
    {
        $this->changeAllToString($review);
        $this->review = $review;
        $this->applyCorrectTypes();

    }

    public function __get($name)
    {
        return $this->review->$name;
    }

    public function __set($variable, $value)
    {
        throw new Exceptions\ImmutableObjectException();
    }

    protected function applyCorrectTypes()
    {
        if (isset($this->review->reviewDate)) {
            $this->review->reviewDate = new DateTimeImmutable($this->reviewDate);
        }

        if (isset($this->review->visitDate)) {
            $this->review->visitDate = new DateTimeImmutable($this->visitDate);
        }

        if (isset($this->review->isDeleted)) {
            $this->review->isDelted = (bool)$this->isDeleted;
        }
        if (isset($this->review->rating)) {
            $this->review->rating = (double)$this->rating;
        }
    }

    protected function changeAllToString($review)
    {
        foreach ($review as &$part) {
            $part = (string)$part;
        }
    }

}

