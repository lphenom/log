<?php

declare(strict_types=1);

namespace LPhenom\Log\Handler;

use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Contract\LogRecord;

/**
 * Discards all log records.
 * Useful as a default no-op handler in tests or when logging is disabled.
 */
final class NullHandler implements HandlerInterface
{
    public function handle(LogRecord $record): void
    {
        // intentionally empty
    }
}
