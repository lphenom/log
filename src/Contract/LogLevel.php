<?php

declare(strict_types=1);

namespace LPhenom\Log\Contract;

/**
 * Log level constants (RFC 5424 severity levels).
 */
final class LogLevel
{
    public const EMERGENCY = 'emergency';
    public const ALERT     = 'alert';
    public const CRITICAL  = 'critical';
    public const ERROR     = 'error';
    public const WARNING   = 'warning';
    public const NOTICE    = 'notice';
    public const INFO      = 'info';
    public const DEBUG     = 'debug';

    /**
     * Severity index: lower value = higher severity.
     *
     * @return array<string,int>
     */
    public static function severityMap(): array
    {
        return [
            self::EMERGENCY => 0,
            self::ALERT     => 1,
            self::CRITICAL  => 2,
            self::ERROR     => 3,
            self::WARNING   => 4,
            self::NOTICE    => 5,
            self::INFO      => 6,
            self::DEBUG     => 7,
        ];
    }

    public static function isValid(string $level): bool
    {
        return array_key_exists($level, self::severityMap());
    }
}
