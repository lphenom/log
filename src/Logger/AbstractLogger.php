<?php

declare(strict_types=1);

namespace LPhenom\Log\Logger;

use LPhenom\Log\Contract\LoggerInterface;
use LPhenom\Log\Contract\LogLevel;
use LPhenom\Log\Contract\LogRecord;
use LPhenom\Log\Exception\InvalidLogLevelException;

/**
 * Abstract base logger that implements all sugar methods.
 *
 * Concrete subclasses must implement writeRecord().
 * This class is KPHP-compatible (no reflection, no magic).
 */
abstract class AbstractLogger implements LoggerInterface
{
    public function __construct(
        protected readonly string $channel = 'app',
    ) {
    }

    abstract protected function writeRecord(LogRecord $record): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if (!LogLevel::isValid($level)) {
            throw new InvalidLogLevelException('Unknown log level: ' . $level);
        }

        $record = new LogRecord(
            timestamp: microtime(true),
            level: $level,
            message: $message,
            channel: $this->channel,
            context: $context,
        );

        $this->writeRecord($record);
    }

    /**
     * @param array<string, scalar|null> $context
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @param array<string, scalar|null> $context
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @param array<string, scalar|null> $context
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @param array<string, scalar|null> $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @param array<string, scalar|null> $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @param array<string, scalar|null> $context
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @param array<string, scalar|null> $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @param array<string, scalar|null> $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
