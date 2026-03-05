<?php

declare(strict_types=1);

namespace LPhenom\Log\Handler;

use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Contract\LogRecord;

/**
 * Delegates each log record to multiple handlers in order.
 */
final class StackHandler implements HandlerInterface
{
    /** @var array<int, HandlerInterface> */
    private array $handlers;

    /**
     * @param array<int, HandlerInterface> $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    public function addHandler(HandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function handle(LogRecord $record): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($record);
        }
    }
}
