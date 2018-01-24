#!/bin/sh
set -e

[ -z "$UPDATE_COVERAGE" ] || composer require satooshi/php-coveralls:v1.1.0
composer install --dev

./vendor/bin/phpunit

[ -z "$UPDATE_COVERAGE" ] || php vendor/bin/coveralls -v
