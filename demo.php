<?php

declare(strict_types=1);

use Oct8pus\Gtag\Gtag;
use Oct8pus\Gtag\PageviewEvent;
use Oct8pus\Gtag\PurchaseEvent;

require_once __DIR__ . '/vendor/autoload.php';

// debug view events are not added to reports
// https://support.google.com/analytics/answer/7201382
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

$random = strtolower(bin2hex(random_bytes(2)));

$page = 'gtag-index.php';
$title = 'My First Web Page';

$event = new PageviewEvent();

$event
    ->setDocumentLocation("http://test.com/{$page}")
    ->setDocumentReferrer('http://test.com/')
    ->setDocumentTitle($title);

//$gtag->send($event);

$event = new PurchaseEvent();

$event
    ->setDocumentLocation('http://test.com/gtag-purchase.php')
    ->setDocumentReferrer('http://test.com/gtag-index.php')
    ->setDocumentTitle('')
    ->setTransactionId(strtoupper(bin2hex(random_bytes(3))))
    ->setTransactionValue(10)
    ->setCurrency('USD')
    ->addItem('pencil', 1, 4.95)
    ->addItem('paper', 2, 2.45);

$gtag->send($event);
