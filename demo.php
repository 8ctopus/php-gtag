<?php

declare(strict_types=1);

use Oct8pus\Gtag\Event;
use Oct8pus\Gtag\Gtag;

require_once __DIR__ . '/vendor/autoload.php';

$gtag = new Gtag([
    '_ga' => 'GA1.1.1827526090.1689745728',
    // expired session
    //'_ga_8XQMZ2E6TH' => 'GS1.1.1689832664.5.1.1689833933.0.0.0',
    // valid session
    '_ga_8XQMZ2E6TH' => 'GS1.1.1689838164.6.0.1689838164.0.0.0',
], true);

$gtag->addParams([
    'user_language' => 'en-us',
    'screen_resolution' => '1920x1080',
]);

$random = strtolower(bin2hex(random_bytes(2)));

$page = 'gtag-index.php';
$title = 'My First Web Page';

$event = new Event([
    'event_name' => 'page_view',
    'document_location' => "http://test.com/{$page}",
    'document_referrer' => 'http://test.com/',
    'document_title' => $title,
]);

$gtag->send($event);
exit;
