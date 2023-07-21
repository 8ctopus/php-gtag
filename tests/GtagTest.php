<?php

declare(strict_types=1);

namespace Tests;

use Oct8pus\Gtag\Gtag;
use Oct8pus\Gtag\Helper;
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
            'gtm' => '45je37h0',
            'ngs_unknown' => 1,
            'external_event' => true,
            'client_id' => str_replace('GA1.1.', '', $clientId),
            'tracking_id' => 'G-8XQMZ2E6TH',
            'session_id' => time(),
            'session_number' => 1,
            'session_engaged' => false,
            'random_p' => 999999999,
            'event_number' => 0,
            'debug' => 'true',
        ];

        self::assertSame($expected, $gtag->params());
    }

    public function testSend() : void
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
            ->addItem('paper and cisors', 1, 4.99);

        $gtag->send($event, true);
    }
}

class GtagMock extends Gtag
{
    public function params() : array
    {
        return $this->params;
    }

    protected function randomP() : self
    {
        $this->params['random_p'] = 999999999;
        $this->params['event_number'] = 0;
        return $this;
    }
}

