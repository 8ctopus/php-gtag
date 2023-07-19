<?php

declare(strict_types=1);

use Oct8pus\Gtag\Event;
use Oct8pus\Gtag\Gtag;
use Oct8pus\Gtag\Payload;

require_once __DIR__ . '/vendor/autoload.php';

//echo "select event type: ";

$gtag = new Gtag([
    'tracking_id' => 'G-8XQMZ2E6TH',
    'client_id' => '1827526090.1689745728',
    'user_language' => 'en-us',
    'screen_resolution' => '1920x1080',
    'debug' => 'true',
]);

$event = new Event([
    'event_name' => 'page_view',
    'document_location' => 'http://test.com/gtag-index.php',
    'document_referrer' => 'http://test.com/',
    'document_title' => 'My First Web Page',
]);

//$payload = $gtag->encode($event);
//echo var_dump($payload);
echo $gtag->ini($event);

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

