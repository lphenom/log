<?php

declare(strict_types=1);

namespace LPhenom\Log\Handler;

use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Contract\LogRecord;
use LPhenom\Log\Exception\LogException;
use LPhenom\Log\Formatter\LineFormatter;

/**
 * Writes log records to a file with:
 *   - size-based log rotation (renames current file to .1, .2, …)
 *
 * KPHP-compatible: no readonly, no constructor property promotion, no trailing commas,
 * no flock() (KPHP does not support LOCK_EX/LOCK_UN constants),
 * try/catch + re-throw pattern instead of try/finally.
 */
final class FileHandler implements HandlerInterface
{
    /** @var string */
    private string $filePath;

    /** @var int */
    private int $maxBytes;

    /** @var int */
    private int $maxFiles;

    /** @var FormatterInterface */
    private FormatterInterface $formatter;

    /**
     * @param string                  $filePath  Absolute path to the log file.
     * @param int                     $maxBytes  Maximum file size before rotation (0 = no rotation).
     * @param int                     $maxFiles  Number of rotated files to keep.
     * @param FormatterInterface|null $formatter
     */
    public function __construct(
        string $filePath,
        int $maxBytes = 10 * 1024 * 1024,
        int $maxFiles = 5,
        ?FormatterInterface $formatter = null
    ) {
        $this->filePath  = $filePath;
        $this->maxBytes  = $maxBytes;
        $this->maxFiles  = $maxFiles;
        $this->formatter = $formatter ?? new LineFormatter();
    }

    public function handle(LogRecord $record): void
    {
        $this->ensureDirectory();

        if ($this->maxBytes > 0 && is_file($this->filePath)) {
            $size = filesize($this->filePath);
            if ($size !== false && $size >= $this->maxBytes) {
                $this->rotate();
            }
        }

        $line = $this->formatter->format($record);

        $fh = fopen($this->filePath, 'a');
        if ($fh === false) {
            throw new LogException('Cannot open log file: ' . $this->filePath);
        }

        // KPHP requires at least one catch — store exception and re-throw after cleanup.
        // Note: flock() is NOT used because LOCK_EX/LOCK_UN constants are not supported in KPHP.
        $exception = null;
        try {
            $written = fwrite($fh, $line);
            if ($written === false) {
                throw new LogException('Failed to write to log file: ' . $this->filePath);
            }
        } catch (\Throwable $e) {
            $exception = $e;
        }

        fclose($fh);

        if ($exception !== null) {
            throw $exception;
        }
    }

    /**
     * Rotate log files: app.log -> app.log.1, app.log.1 -> app.log.2, etc.
     */
    private function rotate(): void
    {
        // Remove the oldest file
        $oldest = $this->filePath . '.' . $this->maxFiles;
        if (is_file($oldest)) {
            unlink($oldest);
        }

        // Shift existing rotated files
        for ($i = $this->maxFiles - 1; $i >= 1; $i--) {
            $src = $this->filePath . '.' . $i;
            $dst = $this->filePath . '.' . ($i + 1);
            if (is_file($src)) {
                rename($src, $dst);
            }
        }

        // Rename current log to .1
        if (is_file($this->filePath)) {
            rename($this->filePath, $this->filePath . '.1');
        }
    }

    private function ensureDirectory(): void
    {
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new LogException('Cannot create log directory: ' . $dir);
            }
        }
    }
}
