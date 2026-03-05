<?php

declare(strict_types=1);

namespace LPhenom\Log\Logger;

use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\LogRecord;
use LPhenom\Log\Handler\FileHandler;

/**
 * A logger that writes all records to a file.
 * Supports size-based rotation via FileHandler.
 */
final class FileLogger extends AbstractLogger
{
    private readonly FileHandler $handler;

    /**
     * @param string                  $filePath  Absolute path to the log file.
     * @param int                     $maxBytes  Max file size before rotation (0 = disabled).
     * @param int                     $maxFiles  Number of rotated files to keep.
     * @param string                  $channel
     * @param FormatterInterface|null $formatter
     */
    public function __construct(
        string $filePath,
        int $maxBytes = 10 * 1024 * 1024,
        int $maxFiles = 5,
        string $channel = 'app',
        ?FormatterInterface $formatter = null,
    ) {
        parent::__construct($channel);
        $this->handler = new FileHandler($filePath, $maxBytes, $maxFiles, $formatter);
    }

    protected function writeRecord(LogRecord $record): void
    {
        $this->handler->handle($record);
    }
}
