<?php

declare(strict_types=1);

namespace LPhenom\Log\Tests\Unit\Formatter;

use LPhenom\Log\Contract\LogRecord;
use LPhenom\Log\Formatter\JsonFormatter;
use LPhenom\Log\Formatter\LineFormatter;
use PHPUnit\Framework\TestCase;

final class FormatterTest extends TestCase
{
    private function makeRecord(string $level = 'info', string $message = 'hello'): LogRecord
    {
        return new LogRecord(
            timestamp: 1700000000.123,
            level: $level,
            message: $message,
            channel: 'app',
            context: ['key' => 'val'],
        );
    }

    public function testLineFormatterContainsLevel(): void
    {
        $formatter = new LineFormatter();
        $output    = $formatter->format($this->makeRecord('error', 'boom'));

        self::assertStringContainsString('ERROR', $output);
        self::assertStringContainsString('boom', $output);
        self::assertStringContainsString('app', $output);
    }

    public function testLineFormatterEndsWithNewline(): void
    {
        $formatter = new LineFormatter();
        $output    = $formatter->format($this->makeRecord());

        self::assertStringEndsWith(PHP_EOL, $output);
    }

    public function testJsonFormatterProducesValidJson(): void
    {
        $formatter = new JsonFormatter();
        $output    = $formatter->format($this->makeRecord('debug', 'msg'));

        $decoded = json_decode(trim($output), true);

        self::assertIsArray($decoded);
        self::assertSame('debug', $decoded['level']);
        self::assertSame('msg', $decoded['message']);
        self::assertSame('app', $decoded['channel']);
        self::assertArrayHasKey('context', $decoded);
    }

    public function testJsonFormatterEndsWithNewline(): void
    {
        $formatter = new JsonFormatter();
        $output    = $formatter->format($this->makeRecord());

        self::assertStringEndsWith(PHP_EOL, $output);
    }
}
