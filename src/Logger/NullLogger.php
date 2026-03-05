<?php

declare(strict_types=1);

namespace LPhenom\Log\Logger;

use LPhenom\Log\Contract\LogRecord;

/**
 * A logger that discards all records.
 * Useful as a safe default when no logging is required.
 */
final class NullLogger extends AbstractLogger
{
    protected function writeRecord(LogRecord $record): void
    {
        // intentionally empty
    }
}
