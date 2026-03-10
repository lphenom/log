<?php

declare(strict_types=1);

namespace LPhenom\Log\Tests\Unit;

use LPhenom\Log\Contract\LogRecord;
use PHPUnit\Framework\TestCase;

final class LogRecordTest extends TestCase
{
    public function testContextJsonEmptyContext(): void
    {
        $record = new LogRecord(
            1700000000.0,
            'info',
            'test',
            'app'
        );
        self::assertSame('{}', $record->contextJson());
    }
    public function testContextJsonWithValues(): void
    {
        // KPHP-compatible context: int|float|string|false|null (no true, use false)
        /** @var array<string, int|float|string|false|null> $context */
        $context = ['user_id' => 42, 'action' => 'login', 'flag' => false, 'empty' => null];
        $record = new LogRecord(
            1700000000.0,
            'error',
            'fail',
            'app',
            $context
        );
        $json = $record->contextJson();
        $decoded = json_decode($json, true);
        self::assertIsArray($decoded);
        self::assertSame(42, $decoded['user_id']);
        self::assertSame('login', $decoded['action']);
        self::assertFalse($decoded['flag']);
        self::assertNull($decoded['empty']);
    }
    public function testRecordPropertiesAreAccessible(): void
    {
        $ts = microtime(true);
        /** @var array<string, int|float|string|false|null> $context */
        $context = ['key' => 'value'];
        $record = new LogRecord(
            $ts,
            'debug',
            'hello',
            'test',
            $context
        );
        self::assertSame($ts, $record->timestamp);
        self::assertSame('debug', $record->level);
        self::assertSame('hello', $record->message);
        self::assertSame('test', $record->channel);
        self::assertSame(['key' => 'value'], $record->context);
    }
}
