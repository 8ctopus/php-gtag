<?php

declare(strict_types=1);

namespace Tests;

use Oct8pus\Gtag\Gtag;
use Oct8pus\Gtag\Helper;
use Oct8pus\Gtag\PageviewEvent;
use Oct8pus\Gtag\PurchaseEvent;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Oct8pus\Gtag\Gtag
 */
final class GtagTest extends TestCase
{
    public function testConstructor() : void
    {
        $clientId = Helper::createClientId();
        $sessionId = Helper::createSessionId();

        $gtag = new GtagMock([
            '_ga' => $clientId,
            '_ga_8XQMZ2E6TH' => $sessionId,
        ], true);

        $expected = [
            'protocol_version' => 2,
            'gtm' => '45je37j0',
            //'ngs_unknown' => 1,
            'external_event' => true,
            'client_id' => str_replace('GA1.1.', '', $clientId),
            'tracking_id' => 'G-8XQMZ2E6TH',
            'session_id' => time(),
            'session_number' => 1,
            'session_engaged' => false,
            'random_p' => 9999999999,
            'event_number' => 0,
            'debug' => 'true',
        ];

        self::assertSame($expected, $gtag->params());
    }

    public function testValidSessionPageviewEvent() : void
    {
        $clientId = Helper::createClientId();
        $sessionId = Helper::createSessionId();

        $gtag = new GtagMock([
            '_ga' => $clientId,
            '_ga_8XQMZ2E6TH' => $sessionId,
        ], true);

        $gtag->addParams([
            'user_language' => 'en-us',
            'screen_resolution' => '1920x1080',
        ]);

        $event = new PageviewEvent();

        $event
            ->setDocumentLocation('http://test.com/purchase.php')
            ->setDocumentReferrer('http://test.com/')
            ->setDocumentTitle('Purchase');

        $gtag->send($event, false);

        $clientId = str_replace('GA1.1.', '', $clientId);
        $timestamp = time();

        $expected = "https://www.google-analytics.com/g/collect?v=2&tid=G-8XQMZ2E6TH&gtm=45je37j0&_p=9999999999&cid={$clientId}&ul=en-us&sr=1920x1080&_s=1&sid={$timestamp}&sct=1&seg=1&dl=http%3A%2F%2Ftest.com%2Fpurchase.php&dr=http%3A%2F%2Ftest.com%2F&dt=Purchase&en=page_view&_ee=1&ep.debug_mode=true";

        self::assertSame($expected, $gtag->curlUrl);
    }

    public function testExpiredSessionPageviewEvent() : void
    {
        $clientId = Helper::createClientId();
        $sessionId = Helper::createExpiredSessionId();

        $gtag = new GtagMock([
            '_ga' => $clientId,
            '_ga_8XQMZ2E6TH' => $sessionId,
        ], true);

        $gtag->addParams([
            'user_language' => 'en-us',
            'screen_resolution' => '1920x1080',
        ]);

        $event = new PageviewEvent();

        $event
            ->setDocumentLocation('http://test.com/purchase.php')
            ->setDocumentReferrer('http://test.com/')
            ->setDocumentTitle('Purchase');

        $gtag->send($event, false);

        $clientId = str_replace('GA1.1.', '', $clientId);
        $timestamp = time();

        // FIX ME on session start, the browser information should be provided
        $expected = "https://www.google-analytics.com/g/collect?v=2&tid=G-8XQMZ2E6TH&gtm=45je37j0&_p=9999999999&cid={$clientId}&ul=en-us&sr=1920x1080&_s=1&sid={$timestamp}&sct=2&seg=0&dl=http%3A%2F%2Ftest.com%2Fpurchase.php&dr=http%3A%2F%2Ftest.com%2F&dt=Purchase&en=page_view&_ss=1&_ee=1&ep.debug_mode=true";

        self::assertSame($expected, $gtag->curlUrl);
    }

    public function testValidSessionPurchaseEvent() : void
    {
        $clientId = Helper::createClientId();
        $sessionId = Helper::createSessionId();

        $gtag = new GtagMock([
            '_ga' => $clientId,
            '_ga_8XQMZ2E6TH' => $sessionId,
        ], true);

        $gtag->addParams([
            'user_language' => 'en-us',
            'screen_resolution' => '1920x1080',
        ]);

        $event = new PurchaseEvent();

        $event
            ->setDocumentLocation('http://test.com/purchase.php')
            ->setDocumentReferrer('http://test.com/')
            ->setDocumentTitle('Purchase')
            ->setTransactionId('T-112')
            ->setTransactionValue(16.97)
            ->setCurrency('USD')
            ->addItem('pen', 2, 5.99)
            ->addItem('paper and cisors', 1, 4.99)
            ->setEngagementTime(100);

        $gtag->send($event, false);

        $clientId = str_replace('GA1.1.', '', $clientId);
        $timestamp = time();

        $expected = "https://www.google-analytics.com/g/collect?v=2&tid=G-8XQMZ2E6TH&gtm=45je37j0&_p=9999999999&cid={$clientId}&ul=en-us&sr=1920x1080&_s=1&cu=USD&sid={$timestamp}&sct=1&seg=1&dl=http%3A%2F%2Ftest.com%2Fpurchase.php&dr=http%3A%2F%2Ftest.com%2F&dt=Purchase&en=purchase&_c=1&_ee=1&pr1=nmpen~pr5.99~qt2&pr2=nmpaper%20and%20cisors~pr4.99~qt1&ep.debug_mode=true&ep.transaction_id=T-112&epn.value=16.97&_et=100";

        self::assertSame($expected, $gtag->curlUrl);
    }
}

class GtagMock extends Gtag
{
    public string $curlUrl;

    public function params() : array
    {
        return $this->params;
    }

    protected function curl(string $url) : self
    {
        $this->curlUrl = $url;
        return $this;
    }

    protected function randomP() : self
    {
        $this->params['random_p'] = 9999999999;
        $this->params['event_number'] = 0;
        return $this;
    }
}
