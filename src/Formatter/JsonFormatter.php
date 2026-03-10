<?php

declare(strict_types=1);

namespace LPhenom\Log\Formatter;

use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\LogRecord;

/**
 * Formats a log record as a single JSON line (JSON Lines / NDJSON).
 *
 * Each record is one JSON object followed by a newline.
 *
 * KPHP note: JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES, JSON_THROW_ON_ERROR
 * are NOT supported in KPHP. We use json_encode with 0 flags and check for false.
 * We encode each field separately and assemble the JSON string manually to avoid
 * mixed-value arrays (KPHP requires uniform array value types).
 */
final class JsonFormatter implements FormatterInterface
{
    public function format(LogRecord $record): string
    {
        $timestamp = json_encode($record->timestamp, 0);
        $channel   = json_encode($record->channel, 0);
        $level     = json_encode($record->level, 0);
        $message   = json_encode($record->message, 0);
        $context   = json_encode($record->context, 0);

        if ($timestamp === false) {
            $timestamp = '0';
        }
        if ($channel === false) {
            $channel = '""';
        }
        if ($level === false) {
            $level = '""';
        }
        if ($message === false) {
            $message = '""';
        }
        if ($context === false) {
            $context = '{}';
        }

        return '{"timestamp":' . $timestamp
            . ',"channel":' . $channel
            . ',"level":' . $level
            . ',"message":' . $message
            . ',"context":' . $context
            . '}' . PHP_EOL;
    }
}
