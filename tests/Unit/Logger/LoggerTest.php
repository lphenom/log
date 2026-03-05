<?php

declare(strict_types=1);

namespace LPhenom\Log\Tests\Unit\Logger;

use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Exception\InvalidLogLevelException;
use LPhenom\Log\Logger\NullLogger;
use LPhenom\Log\Logger\StackLogger;
use LPhenom\Log\Logger\StdoutLogger;
use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    public function testNullLoggerDoesNotThrow(): void
    {
        $logger = new NullLogger('test');
        $logger->info('message');
        $logger->debug('debug', ['key' => 'value']);
        $logger->error('error');
        self::assertTrue(true);
    }

    public function testAllSugarMethodsCallLog(): void
    {
        $logger = new NullLogger();

        // These must not throw
        $logger->emergency('em');
        $logger->alert('al');
        $logger->critical('cr');
        $logger->error('er');
        $logger->warning('wa');
        $logger->notice('no');
        $logger->info('in');
        $logger->debug('de');

        self::assertTrue(true);
    }

    public function testInvalidLevelThrows(): void
    {
        $this->expectException(InvalidLogLevelException::class);
        $logger = new NullLogger();
        $logger->log('not_a_level', 'msg');
    }

    public function testStdoutLoggerOutputsMessage(): void
    {
        $logger = new StdoutLogger('app');
        ob_start();
        $logger->info('stdout test');
        $output = ob_get_clean();

        self::assertIsString($output);
        self::assertStringContainsString('stdout test', $output);
    }

    public function testStackLoggerWithNoHandlers(): void
    {
        $logger = new StackLogger([], 'stack');
        $logger->info('silent');
        self::assertTrue(true);
    }

    public function testStackLoggerCallsHandlers(): void
    {
        $calls   = 0;
        $handler = new class ($calls) implements HandlerInterface {
            public int $calls = 0;
            public function __construct(int &$c)
            {
                $this->calls = &$c;
            }
            public function handle(\LPhenom\Log\Contract\LogRecord $record): void
            {
                $this->calls++;
            }
        };

        $logger = new StackLogger([$handler], 'test');
        $logger->info('hi');
        $logger->error('oops');

        self::assertSame(2, $handler->calls);
    }

    public function testContextIsPassedToRecord(): void
    {
        $records = [];

        $handler = new class ($records) implements HandlerInterface {
            /** @var \LPhenom\Log\Contract\LogRecord[] */
            public array $records = [];
            /**
             * @param \LPhenom\Log\Contract\LogRecord[] $r
             */
            public function __construct(array &$r)
            {
                $this->records = &$r;
            }
            public function handle(\LPhenom\Log\Contract\LogRecord $record): void
            {
                $this->records[] = $record;
            }
        };

        $logger = new StackLogger([$handler]);
        $logger->info('ctx test', ['user_id' => 99, 'action' => 'click']);

        self::assertCount(1, $handler->records);
        self::assertSame(99, $handler->records[0]->context['user_id']);
        self::assertSame('click', $handler->records[0]->context['action']);
    }
}
