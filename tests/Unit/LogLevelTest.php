<?php

declare(strict_types=1);

namespace LPhenom\Log\Tests\Unit;

use LPhenom\Log\Contract\LogLevel;
use PHPUnit\Framework\TestCase;

final class LogLevelTest extends TestCase
{
    public function testAllLevelsAreValid(): void
    {
        $levels = [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ];

        foreach ($levels as $level) {
            self::assertTrue(LogLevel::isValid($level), "Level '$level' should be valid");
        }
    }

    public function testUnknownLevelIsInvalid(): void
    {
        self::assertFalse(LogLevel::isValid('unknown'));
        self::assertFalse(LogLevel::isValid(''));
    }

    public function testSeverityMapIsComplete(): void
    {
        $map = LogLevel::severityMap();
        self::assertCount(8, $map);
        self::assertSame(0, $map[LogLevel::EMERGENCY]);
        self::assertSame(7, $map[LogLevel::DEBUG]);
    }
}
