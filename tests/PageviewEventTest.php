<?php

declare(strict_types=1);

namespace Tests;

use Oct8pus\Gtag\PageviewEvent;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Oct8pus\Gtag\AbstractEvent
 * @covers \Oct8pus\Gtag\PageviewEvent
 */
final class PageviewEventTest extends TestCase
{
    public function test() : void
    {
        $event = new PageviewEvent();

        $event
            ->setDocumentLocation('http://test.com/purchase.php')
            ->setDocumentReferrer('http://test.com/')
            ->setDocumentTitle('Purchase');

        $event->valid();

        self::assertSame($event->name(), 'page_view');

        $expected = [
            'dl' => 'http://test.com/purchase.php',
            'dr' => 'http://test.com/',
            'dt' => 'Purchase',
            'en' => 'page_view',
        ];

        self::assertSame($expected, $event->encode([]));

        $expected = <<<'TEXT'
        dl: http://test.com/purchase.php
        dr: http://test.com/
        dt: Purchase
        en: page_view

        TEXT;

        self::assertSame($expected, $event->ini([]));
    }
}
