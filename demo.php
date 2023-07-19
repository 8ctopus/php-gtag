<?php

declare(strict_types=1);

use Oct8pus\Gtag\Payload;

require_once __DIR__ . '/vendor/autoload.php';

echo "enter payload:\n";

$source = stream_get_line(STDIN, 1024, PHP_EOL . PHP_EOL);

$source = str_replace("\r", '', $source);

$converted = (new Payload())
    ->analyze($source);

echo "enter file name: ";
$filename = trim(fgets(STDIN));

$file = __DIR__ . "/events/{$filename}.event";

file_put_contents($file, $converted . "\n\noriginal payload\n" . $source . "\n");
