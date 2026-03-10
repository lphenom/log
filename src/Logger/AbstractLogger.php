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
 * KPHP-compatible: no readonly, no constructor property promotion, no trailing commas.
 * PHPDoc uses false instead of bool for KPHP array generics.
 */
abstract class AbstractLogger implements LoggerInterface
{
    /** @var string */
    protected string $channel;

    public function __construct(string $channel = 'app')
    {
        $this->channel = $channel;
    }

    abstract protected function writeRecord(LogRecord $record): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if (!LogLevel::isValid($level)) {
            throw new InvalidLogLevelException('Unknown log level: ' . $level);
        }
        $record = new LogRecord(
            microtime(true),
            $level,
            $message,
            $this->channel,
            $context
        );
        $this->writeRecord($record);
    }

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
