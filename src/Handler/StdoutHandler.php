<?php

declare(strict_types=1);

namespace LPhenom\Log\Handler;

use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Contract\LogRecord;
use LPhenom\Log\Formatter\LineFormatter;

/**
 * Writes log records to STDOUT.
 *
 * KPHP-compatible: no readonly, no constructor property promotion.
 */
final class StdoutHandler implements HandlerInterface
{
    /** @var FormatterInterface */
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new LineFormatter();
    }

    public function handle(LogRecord $record): void
    {
        echo $this->formatter->format($record);
    }
}
