<?php

declare(strict_types=1);

namespace Tests;

use Oct8pus\Gtag\AbstractEvent;
use Oct8pus\Gtag\PurchaseEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractEvent::class)]
#[CoversClass(PurchaseEvent::class)]
final class PurchaseEventTest extends TestCase
{
    public function test() : void
    {
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

        $event->valid();

        self::assertSame($event->name(), 'purchase');

        $expected = [
            'cu' => 'USD',
            'dl' => 'http://test.com/purchase.php',
            'dr' => 'http://test.com/',
            'dt' => 'Purchase',
            'en' => 'purchase',
            '_c' => true,
            'pr1' => 'nmpen~pr5.99~qt2',
            'pr2' => 'nmpaper and cisors~pr4.99~qt1',
            'ep.transaction_id' => 'T-112',
            'epn.value' => 16.97,
            '_et' => 100,
        ];

        self::assertSame($expected, $event->encode([]));

        $expected = <<<'TEXT'
        cu: USD
        dl: http://test.com/purchase.php
        dr: http://test.com/
        dt: Purchase
        en: purchase
        _c: 1
        pr1: nmpen~pr5.99~qt2
        pr2: nmpaper and cisors~pr4.99~qt1
        ep.transaction_id: T-112
        epn.value: 16.97
        _et: 100

        TEXT;

        self::assertSame($expected, $event->ini([]));
    }
}
