<?php

declare(strict_types=1);

namespace LPhenom\Log\Formatter;

use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\LogRecord;

/**
 * Formats a log record as a single human-readable line.
 *
 * Example output:
 *   [2024-01-01 12:00:00.123] app.ERROR: Something went wrong {"user_id":42}
 *
 * Uses PHP date format 'Y-m-d H:i:s' + manual milliseconds extracted from the
 * fractional part of the Unix timestamp — KPHP-safe, no fmod/number_format tricks.
 */
final class LineFormatter implements FormatterInterface
{
    public function __construct(
        private readonly string $dateFormat = 'Y-m-d H:i:s',
    ) {
    }

    public function format(LogRecord $record): string
    {
        $ts      = $record->timestamp;
        $sec     = (int) $ts;
        $ms      = (int) round(($ts - $sec) * 1000);
        $date    = date($this->dateFormat, $sec) . '.' . str_pad((string) $ms, 3, '0', STR_PAD_LEFT);
        $level   = strtoupper($record->level);
        $context = $record->contextJson();

        return sprintf(
            '[%s] %s.%s: %s %s',
            $date,
            $record->channel,
            $level,
            $record->message,
            $context,
        ) . PHP_EOL;
    }
}
