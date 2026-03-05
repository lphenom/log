<?php

declare(strict_types=1);

namespace LPhenom\Log\Contract;

/**
 * Contract for log formatters.
 *
 * A formatter converts a LogRecord into a plain string
 * that can be written to an output stream or file.
 */
interface FormatterInterface
{
    public function format(LogRecord $record): string;
}

