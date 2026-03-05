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
 * KPHP note: we avoid building a mixed-value array for json_encode because
 * KPHP requires uniform array value types. Instead we encode each field
 * separately and assemble the JSON string manually.
 */
final class JsonFormatter implements FormatterInterface
{
    public function format(LogRecord $record): string
    {
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;

        $timestamp = json_encode($record->timestamp, $flags);
        $channel   = json_encode($record->channel, $flags);
        $level     = json_encode($record->level, $flags);
        $message   = json_encode($record->message, $flags);
        $context   = json_encode($record->context, $flags);

        return '{"timestamp":' . $timestamp
            . ',"channel":' . $channel
            . ',"level":' . $level
            . ',"message":' . $message
            . ',"context":' . $context
            . '}' . PHP_EOL;
    }
}
