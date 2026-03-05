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
            timestamp: 1700000000.0,
            level: 'info',
            message: 'test',
            channel: 'app',
        );

        self::assertSame('{}', $record->contextJson());
    }

    public function testContextJsonWithValues(): void
    {
        $record = new LogRecord(
            timestamp: 1700000000.0,
            level: 'error',
            message: 'fail',
            channel: 'app',
            context: ['user_id' => 42, 'action' => 'login', 'flag' => true, 'empty' => null],
        );

        $json = $record->contextJson();
        $decoded = json_decode($json, true);

        self::assertIsArray($decoded);
        self::assertSame(42, $decoded['user_id']);
        self::assertSame('login', $decoded['action']);
        self::assertTrue($decoded['flag']);
        self::assertNull($decoded['empty']);
    }

    public function testRecordPropertiesAreReadonly(): void
    {
        $ts = microtime(true);
        $record = new LogRecord(
            timestamp: $ts,
            level: 'debug',
            message: 'hello',
            channel: 'test',
            context: ['key' => 'value'],
        );

        self::assertSame($ts, $record->timestamp);
        self::assertSame('debug', $record->level);
        self::assertSame('hello', $record->message);
        self::assertSame('test', $record->channel);
        self::assertSame(['key' => 'value'], $record->context);
    }
}
