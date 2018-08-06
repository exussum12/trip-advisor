# Trip Advisor Review Client
[![Build Status](https://travis-ci.org/exussum12/trip-advisor.svg?branch=master)](https://travis-ci.org/exussum12/trip-advisor)
[![Coverage Status](https://coveralls.io/repos/github/exussum12/trip-advisor/badge.svg?branch=master)](https://coveralls.io/github/exussum12/trip-advisor?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/exussum12/trip-advisor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/exussum12/trip-advisor/?branch=master)


This is designed to be an easy way in php to use the reviews API from TripAdvisor.

This can be installed using composer for example

    composer require exussum12/trip-advisor
    
 Then the most simple method of getting the reviews is as follows
 
```php
$reviews = new exussum12\TripAdvisor\Reviews('your key', 'your secret');
foreach ($reviews->get() as $review) {
    //handle review
}
```

The API is paged at a maximum of 1000 reviews per page, This is all handled transparently and will page internally
only requesting new data when it is required.

You can set the page size by doing `$reviews->limit(50);` This will request smaller chucks.
If you only want a certain size of data use `$reviews->get()->getArray()` which will only send one request.

If you are trying to update existing data, use a date range, eg  `$reviews->since(new DateTime('2017-01-01'))`

This will only return the newer reviews.
# Options
All options can be chained, together for example

```php
$reviews->offset(100)->limit(50)->since(new DateTime('2017-01-31'));
foreach ($reviews->get() as $review) {
    //handle review
}
```

# Exceptions
A few things can go wrong in this process, The exceptions are named as well as I can think of, All exceptions extend
from `exussum12\TripAdvisor\Exceptions\BaseException`.

The reviews which come back are immutable, So trying to write to any review will throw an exception (ImmutableObjectException)

