<?php

declare(strict_types=1);

namespace LPhenom\Log\Contract;

/**
 * Contract for log handlers.
 *
 * A handler receives a LogRecord and writes/processes it.
 */
interface HandlerInterface
{
    /**
     * Handle the log record.
     *
     * @param LogRecord $record
     */
    public function handle(LogRecord $record): void;
}
