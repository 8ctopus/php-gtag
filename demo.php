<?php

declare(strict_types=1);

use Oct8pus\Gtag\Event;
use Oct8pus\Gtag\Payload;

require_once __DIR__ . '/vendor/autoload.php';

//echo "select event type: ";

$type = 'page_view'; //trim(fgets(STDIN));
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

$event = new Event($type, $known, true);

//$payload = $event->encode();
//echo var_dump($payload);

echo $event->ini();

exit;

echo "enter payload:\n";

$source = stream_get_line(STDIN, 1024, PHP_EOL . PHP_EOL);

$source = str_replace("\r", '', $source);

$converted = (new Payload())
    ->analyze($source);

echo "enter file name: ";
$filename = trim(fgets(STDIN));

$file = __DIR__ . "/events/{$filename}.event";

file_put_contents($file, $converted . "\n\noriginal payload\n" . $source . "\n");

