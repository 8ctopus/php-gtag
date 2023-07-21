<?php

declare(strict_types=1);

use Oct8pus\Gtag\Payload;

require_once __DIR__ . '/vendor/autoload.php';

echo "enter payload:\n";

$source = stream_get_line(STDIN, 1024, PHP_EOL . PHP_EOL);

$source = str_replace("\r", '', $source);

$converted = (new Payload())
    ->analyze($source);

echo $converted;

echo "\n\nenter file name to save or press enter to exit: ";

$filename = trim(fgets(STDIN));

if (empty($filename)) {
    exit;
}

$file = __DIR__ . "/events/{$filename}.event";

file_put_contents($file, $converted . "\n\noriginal payload\n" . $source . "\n");
