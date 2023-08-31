# php gtag.js

Experimental Google Analytics 4 gtag.js php implementation for server side tracking.

## why?

Google Analytics 4 measurement protocol is limited in what it can measure.

The objective of this library is to use the much more powerful gtag.js API for server side measurements.

I'm using the library for some personal projects (tracking of PayPal sales) and it works, however it's far from a perfect replication of what gtag.js does. Use it at your own risk.

Contributions welcome!

## install

I've not published the library on `packagist.org` because it's experimental.

- add the repository to `composer.json`

```json
"repositories": [{
    "type": "git",
    "url": "https://github.com/8ctopus/php-gtag"
}],
```

- install

    composer require 8ctopus/php-gtag

## demo

Check `demo.php` on how to use it.
