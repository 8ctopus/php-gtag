<?php

declare(strict_types=1);

namespace Oct8pus\Gtag;

$required = [
    'page_view' => [
        'protocol_version',
        'tracking_id',
        'gtm',
        'random_p',
        'client_id',
        'user_language',
        'screen_resolution',
        'ngs_unknown',
        'event_number',
        'session_id',
        'session_number',
        'session_engaged',
        'document_location',
        'document_referrer',
        'document_title',
        'event_name',
        'external_event',
        'engagement_time',
    ],
    'purchase' => [
        'protocol_version',
        'tracking_id',
        'gtm',
        'random_p',
        'client_id',
        'user_language',
        'screen_resolution',
        'ngs_unknown',
        'event_number',
        'currency',
        'session_id',
        'session_number',
        'session_engaged',
        'document_location',
        'document_referrer',
        'document_title',
        'event_name',
        'conversion',
        'external_event',
        'product_1',
        'product_2',
        'debug',
        'transaction_id',
        'transaction_value',
        'engagement_time',
    ],
];

$known = [
    'protocol_version' => 2,
    'tracking_id' => 'G-8XQMZ2E6TH',
    'gtm' => '45je37h0',
    'random_p' => rand(1, 999999999),
    'client_id' => '1827526090.1689745728',
    'user_language' => 'en-us',
    'screen_resolution' => '1920x1080',
    'ngs_unknown' => 1,
    'event_number' => 1,
    'currency' => 'USD',
    'session_id' => time() - 10,
    'session_number' => 1,
    'session_engaged' => true,
    'document_location' => 'http://test.com/gtag-index.php',
    'document_referrer' => 'http://test.com/',
    'document_title' => 'My First Web Page',
    'external_event' => true,
    'engagement_time' => 1,
];

//echo "select event type: ";

$type = 'page_view'; //trim(fgets(STDIN));

if (!array_key_exists($type, $required)) {
    throw new Exception("unhandled event type - {$type}");
}

$required = $required[$type];
$known['event_name'] = $type;

$toSend = [];

foreach ($required as $key) {
    if (array_key_exists($key, $known)) {
        $toSend[$key] = $known[$key];
    } else {
        $toSend[$key] = null;
    }
}

echo var_dump($toSend);
