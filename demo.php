<?php

declare(strict_types=1);

use Oct8pus\Gtag\Event;
use Oct8pus\Gtag\Gtag;
use Oct8pus\Gtag\Payload;

require_once __DIR__ . '/vendor/autoload.php';

$gtag = new Gtag([
    '_ga' => 'GA1.1.1827526090.1689745728',
    '_ga_8XQMZ2E6TH' => 'GS1.1.1689832664.5.1.1689833933.0.0.0',
], true);

$gtag->addParams([
    'user_language' => 'en-us',
    'screen_resolution' => '1920x1080',
]);

$random = strtolower(bin2hex(random_bytes(2)));

$event = new Event([
    'event_name' => 'page_view',
    'document_location' => "http://test.com/{$random}/",
    'document_referrer' => 'http://test.com/',
    'document_title' => $random,
]);

echo $gtag->ini($event);
//$payload = $gtag->encode($event);
//echo var_dump($payload);

exit;
$gtag->send($event);

$random = strtolower(bin2hex(random_bytes(2)));

$event = new Event([
    'event_name' => 'page_view',
    'document_location' => "http://test.com/{$random}/",
    'document_referrer' => 'http://test.com/',
    'document_title' => $random,
]);

$gtag->send($event);

echo "enter payload:\n";

$source = stream_get_line(STDIN, 1024, PHP_EOL . PHP_EOL);

$source = str_replace("\r", '', $source);

$converted = (new Payload())
    ->analyze($source);

echo "enter file name: ";
$filename = trim(fgets(STDIN));

$file = __DIR__ . "/events/{$filename}.event";

file_put_contents($file, $converted . "\n\noriginal payload\n" . $source . "\n");

