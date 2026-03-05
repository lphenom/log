<?php

declare(strict_types=1);

namespace LPhenom\Log\Formatter;

use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\LogRecord;

/**
 * Formats a log record as a single human-readable line.
 *
 * Example output:
 *   [2024-01-01 12:00:00.000] app.ERROR: Something went wrong {"user_id":42}
 */
final class LineFormatter implements FormatterInterface
{
    public function __construct(
        private readonly string $dateFormat = 'Y-m-d H:i:s.v',
    ) {
    }

    public function format(LogRecord $record): string
    {
        $date    = date($this->dateFormat, (int) $record->timestamp)
            . substr(number_format(fmod($record->timestamp, 1.0), 3), 1);
        $level   = strtoupper($record->level);
        $channel = $record->channel;
        $context = $record->contextJson();

        return sprintf(
            '[%s] %s.%s: %s %s',
            $date,
            $channel,
            $level,
            $record->message,
            $context,
        ) . PHP_EOL;
    }
}
