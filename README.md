# php gtag.js

[![packagist](https://poser.pugx.org/8ctopus/php-gtag/v)](https://packagist.org/packages/8ctopus/php-gtag)
[![downloads](https://poser.pugx.org/8ctopus/php-gtag/downloads)](https://packagist.org/packages/8ctopus/php-gtag)
[![min php version](https://poser.pugx.org/8ctopus/php-gtag/require/php)](https://packagist.org/packages/8ctopus/php-gtag)
[![license](https://poser.pugx.org/8ctopus/php-gtag/license)](https://packagist.org/packages/8ctopus/php-gtag)
[![tests](https://github.com/8ctopus/php-gtag/actions/workflows/tests.yml/badge.svg)](https://github.com/8ctopus/php-gtag/actions/workflows/tests.yml)
![code coverage badge](https://raw.githubusercontent.com/8ctopus/php-gtag/image-data/coverage.svg)
![lines of code](https://raw.githubusercontent.com/8ctopus/php-gtag/image-data/lines.svg)

Experimental Google Analytics 4 gtag.js php implementation for server side tracking.

## why?

Google Analytics 4 measurement protocol is limited in what it can measure.

The objective of this library is to use the much more powerful gtag.js API for server side measurements.

I'm using the library for some personal projects (tracking of PayPal sales) and it works, however it's far from a perfect replication of what gtag.js does. Use it at your own risk.

Contributions welcome!

## install

    composer require 8ctopus/php-gtag

## demo

Check `demo.php` on how to use it.

## cookies

The cookies are described [here](https://github.com/8ctopus/php-gtag/blob/master/cookies.md)
