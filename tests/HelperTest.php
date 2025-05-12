<?php

declare(strict_types=1);

namespace Tests;

use Oct8pus\Gtag\Helper;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Oct8pus\Gtag\Helper
 */
final class HelperTest extends TestCase
{
    public function testCreateClientId() : void
    {
        $clientId = Helper::createClientId();

        self::assertMatchesRegularExpression('/^GA1\.2\.\d{6,10}\.\d{10}$/', $clientId);

        $clientId = HelperMock::createClientId();

        self::assertSame('GA1.2.4444444444.' . time(), $clientId);
    }

    public function testCreateSessionId() : void
    {
        $sessionId = Helper::createSessionId();

        self::assertMatchesRegularExpression('/^GS2\.1\.s(\d{10})\$o(\d{1,2})\$g(0|1)\$t(\d{10})\$j\d\$l\d\$h\d$/', $sessionId);

        $sessionId = HelperMock::createSessionId();

        self::assertSame('GS2.1.s' . time() . '$o1$g0$t' . time() . '$j0$l0$h0', $sessionId);
    }

    public function testCreateCookies() : void
    {
        $trackingId = 'G-9XQMZ2E6TH';

        $cookies = Helper::createCookies($trackingId);

        self::assertMatchesRegularExpression('/^GA1\.2\.\d{6,10}\.\d{10}$/', $cookies['_ga']);

        $trackingId = str_replace('G-', '', $trackingId);

        $cookie = "_ga_{$trackingId}";

        self::assertMatchesRegularExpression('/^GS2\.1\.s(\d{10})\$o(\d{1,2})\$g(0|1)\$t(\d{10})\$j\d\$l\d\$h\d$/', $cookies[$cookie]);
    }
}

class HelperMock extends Helper
{
    protected static function randomInt() : int
    {
        return 4444444444;
    }
}
