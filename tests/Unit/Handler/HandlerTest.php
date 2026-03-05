<?php

declare(strict_types=1);

namespace LPhenom\Log\Tests\Unit\Handler;

use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Contract\LogRecord;
use LPhenom\Log\Handler\NullHandler;
use LPhenom\Log\Handler\StackHandler;
use LPhenom\Log\Handler\StdoutHandler;
use PHPUnit\Framework\TestCase;

final class HandlerTest extends TestCase
{
    private function makeRecord(): LogRecord
    {
        return new LogRecord(
            timestamp: microtime(true),
            level: 'info',
            message: 'test message',
            channel: 'app',
        );
    }

    public function testNullHandlerDoesNothing(): void
    {
        $handler = new NullHandler();
        // must not throw
        $handler->handle($this->makeRecord());
        self::assertTrue(true);
    }

    public function testStdoutHandlerOutputsText(): void
    {
        $handler = new StdoutHandler();
        ob_start();
        $handler->handle($this->makeRecord());
        $output = ob_get_clean();

        self::assertIsString($output);
        self::assertStringContainsString('test message', $output);
    }

    public function testStackHandlerCallsAllHandlers(): void
    {
        $countA = 0;
        $countB = 0;

        $mockA = new class ($countA) implements HandlerInterface {
            public int $count = 0;
            public function __construct(int &$c)
            {
                $this->count = &$c;
            }
            public function handle(LogRecord $record): void
            {
                $this->count++;
            }
        };

        $mockB = new class ($countB) implements HandlerInterface {
            public int $count = 0;
            public function __construct(int &$c)
            {
                $this->count = &$c;
            }
            public function handle(LogRecord $record): void
            {
                $this->count++;
            }
        };

        $stack = new StackHandler([$mockA, $mockB]);
        $stack->handle($this->makeRecord());

        self::assertSame(1, $mockA->count);
        self::assertSame(1, $mockB->count);
    }

    public function testStackHandlerAddHandler(): void
    {
        $calls = 0;
        $mock  = new class ($calls) implements HandlerInterface {
            public int $calls = 0;
            public function __construct(int &$c)
            {
                $this->calls = &$c;
            }
            public function handle(LogRecord $record): void
            {
                $this->calls++;
            }
        };

        $stack = new StackHandler();
        $stack->addHandler($mock);
        $stack->handle($this->makeRecord());

        self::assertSame(1, $mock->calls);
    }
}
