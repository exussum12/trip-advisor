# Trip Advisor Review Client

This is designed to be an easy way in php to use the reviews API from trip advisor.

This can be installed using composer for example

    composer require exussum12\TripAdvisor
    
 Then the most simple method of getting the reviews is as follows
 
```php
$reviews = new exussum12\TripAdvisor\Reviews('your key', 'your secret');
forech ($reviews->get() as $review) {
    //handle review
}
```

You The API is paged at a maximum of 1000 reviews per page, This is all handled transparently and will page internally
only requesting new data when it it is required.

You can set the page size by doing `$reviews->limit(50);` This will request smaller chucks.
If you only want a certain size of data use `$reviews->get()->getArray()` which will only send one request.

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

