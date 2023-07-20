<?php

declare(strict_types=1);

use Oct8pus\Gtag\Event;
use Oct8pus\Gtag\Gtag;

require_once __DIR__ . '/vendor/autoload.php';

// debug view https://support.google.com/analytics/answer/7201382
$gtag = new Gtag([
    '_ga' => 'GA1.1.1827526090.1689745728',
    // expired session
    '_ga_8XQMZ2E6TH' => 'GS1.1.1689832664.9.1.1689833933.0.0.0',
    // valid session
    //'_ga_8XQMZ2E6TH' => 'GS1.1.1689838164.6.1.1689840046.0.0.0',
], true);

$gtag->addParams([
    'user_language' => 'en-us',
    'screen_resolution' => '1920x1080',
]);

/*
$random = strtolower(bin2hex(random_bytes(2)));

$page = 'gtag-index.php';
$title = 'My First Web Page';

$event = new Event([
    'event_name' => 'page_view',

    'document_location' => "http://test.com/{$page}",
    'document_referrer' => 'http://test.com/',
    'document_title' => $title,
]);
*/

/*
$event = new Event([
    'event_name' => 'page_view',

    'document_location' => "http://test.com/gtag-purchase.php",
    'document_referrer' => 'http://test.com/gtag-index.php',
    'document_title' => '',
]);

$gtag->send($event);
*/

$event = new Event([
    'event_name' => 'purchase',

    'document_location' => "http://test.com/gtag-purchase.php",
    'document_referrer' => 'http://test.com/gtag-index.php',
    'document_title' => '',

    'conversion' => true,
    'transaction_id' => strtoupper(bin2hex(random_bytes(3))),
    'currency' => 'USD',
    'transaction_value' => 10,
    'product_1' => 'nmpaper~qt1~pr5',
    'product_2' => 'nmpencil~qt1~pr5',

    //'engagement_time' => 10,
]);

$gtag->send($event);

exit;
