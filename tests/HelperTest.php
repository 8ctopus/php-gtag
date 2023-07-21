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

        static::assertMatchesRegularExpression('/^GA1\.1\.\d{10}\.\d{10}$/', $clientId);

        $clientId = HelperMock::createClientId();

        static::assertSame('GA1.1.4444444444.' . time(), $clientId);
    }

    public function testCreateSessionId() : void
    {
        $sessionId = Helper::createSessionId();

        static::assertMatchesRegularExpression('/^GS1\.1\.(\d{10})\.(\d{1,2})\.(0|1)\.(\d{10})\.\d\.\d\.\d$/', $sessionId);

        $sessionId = HelperMock::createSessionId();

        static::assertSame('GS1.1.' . time() . '.1.0.' . time() . '.0.0.0', $sessionId);
    }
}

class HelperMock extends Helper
{
    protected static function randomInt() : int
    {
        return 4444444444;
    }
}
