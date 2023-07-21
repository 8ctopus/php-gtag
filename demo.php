<?php

declare(strict_types=1);

use Oct8pus\Gtag\Gtag;
use Oct8pus\Gtag\PageviewEvent;
use Oct8pus\Gtag\PurchaseEvent;

require_once __DIR__ . '/vendor/autoload.php';

// debug view events are not added to reports
// https://support.google.com/analytics/answer/7201382
if (!file_exists('.config.php')) {
    echo 'enter _ga cookie value: ';
    $ga = trim(fgets(STDIN));

    echo 'enter _ga_* cookie name: ';
    $name = trim(fgets(STDIN));

    echo 'enter _ga_* cookie value: ';
    $value = trim(fgets(STDIN));

    $config = [
        '_ga' => $ga,
        "{$name}" => $value,
    ];

    file_put_contents('.config.php', '<?php $config = ' . var_export($config, true) . ';');
}

require '.config.php';

$gtag = new Gtag($config, true);

$gtag->addParams([
    'user_language' => 'en-us',
    'screen_resolution' => '1920x1080',
]);

$random = strtolower(bin2hex(random_bytes(2)));

$page = 'gtag-index.php';
$title = 'My First Web Page';

$event = new PageviewEvent();

$event
    ->setDocumentLocation("http://test.com/{$page}")
    ->setDocumentReferrer('http://test.com/')
    ->setDocumentTitle($title);

//$gtag->send($event, true);

$event = new PurchaseEvent();

$event
    ->setDocumentLocation('http://test.com/gtag-purchase.php')
    ->setDocumentReferrer('http://test.com/gtag-index.php')
    ->setDocumentTitle('')
    ->setTransactionId(strtoupper(bin2hex(random_bytes(3))))
    ->setTransactionValue(16.97)
    ->setCurrency('USD')
    ->addItem('pen', 2, 5.99)
    ->addItem('paper and cisors', 1, 4.99);

$gtag->send($event, true);
