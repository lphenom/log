<?php

declare(strict_types=1);

namespace LPhenom\Log\Formatter;

use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\LogRecord;

/**
 * Formats a log record as a single JSON line (JSON Lines / NDJSON).
 *
 * Each record is one JSON object followed by a newline.
 */
final class JsonFormatter implements FormatterInterface
{
    public function format(LogRecord $record): string
    {
        $data = [
            'timestamp' => $record->timestamp,
            'channel'   => $record->channel,
            'level'     => $record->level,
            'message'   => $record->message,
            'context'   => $record->context,
        ];

        $encoded = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );

        return $encoded . PHP_EOL;
    }
}
