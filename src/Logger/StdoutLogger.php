<?php

declare(strict_types=1);

namespace LPhenom\Log\Logger;

use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\LogRecord;
use LPhenom\Log\Handler\StdoutHandler;

/**
 * A logger that writes all records to STDOUT.
 */
final class StdoutLogger extends AbstractLogger
{
    private readonly StdoutHandler $handler;

    public function __construct(
        string $channel = 'app',
        ?FormatterInterface $formatter = null,
    ) {
        parent::__construct($channel);
        $this->handler = new StdoutHandler($formatter);
    }

    protected function writeRecord(LogRecord $record): void
    {
        $this->handler->handle($record);
    }
}
