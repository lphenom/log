<?php

declare(strict_types=1);

namespace LPhenom\Log\Logger;

use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Contract\LogRecord;

/**
 * A logger that delegates each record to multiple handlers.
 *
 * This allows combining several output targets (file + stdout, etc.)
 * without creating a new AbstractLogger subclass.
 */
final class StackLogger extends AbstractLogger
{
    /** @var array<int, HandlerInterface> */
    private array $handlers;

    /**
     * @param array<int, HandlerInterface> $handlers
     * @param string                       $channel
     */
    public function __construct(array $handlers = [], string $channel = 'app')
    {
        parent::__construct($channel);
        $this->handlers = $handlers;
    }

    public function addHandler(HandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    protected function writeRecord(LogRecord $record): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($record);
        }
    }
}
