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
    public function testCreateCookies() : void
    {
        $trackingId = 'G-9XQMZ2E6TH';

        Helper::createCookies($trackingId);

        self::assertMatchesRegularExpression('/^GA1\.1\.\d{9,10}\.\d{10}$/', $_COOKIE['_ga']);

        $trackingId = str_replace('G-', '', $trackingId);

        $cookie = "_ga_{$trackingId}";

        self::assertMatchesRegularExpression('/^GS1\.1\.(\d{10})\.(\d{1,2})\.(0|1)\.(\d{10})\.\d\.\d\.\d$/', $_COOKIE[$cookie]);
    }

    public function testCreateClientId() : void
    {
        $clientId = Helper::createClientId();

        self::assertMatchesRegularExpression('/^GA1\.1\.\d{9,10}\.\d{10}$/', $clientId);

        $clientId = HelperMock::createClientId();

        self::assertSame('GA1.1.4444444444.' . time(), $clientId);
    }

    public function testCreateSessionId() : void
    {
        $sessionId = Helper::createSessionId();

        self::assertMatchesRegularExpression('/^GS1\.1\.(\d{10})\.(\d{1,2})\.(0|1)\.(\d{10})\.\d\.\d\.\d$/', $sessionId);

        $sessionId = HelperMock::createSessionId();

        self::assertSame('GS1.1.' . time() . '.1.0.' . time() . '.0.0.0', $sessionId);
    }
}

class HelperMock extends Helper
{
    protected static function randomInt() : int
    {
        return 4444444444;
    }
}
