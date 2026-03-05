<?php

declare(strict_types=1);

namespace LPhenom\Log\Contract;

/**
 * PSR-3-inspired logger interface (KPHP-compatible, no magic).
 *
 * Context values MUST be scalar or null.
 * No mixed/object context is allowed (KPHP strict typing).
 */
interface LoggerInterface
{
    /**
     * @param array<string, scalar|null> $context
     */
    public function log(string $level, string $message, array $context = []): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function alert(string $message, array $context = []): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function critical(string $message, array $context = []): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function error(string $message, array $context = []): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function warning(string $message, array $context = []): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function notice(string $message, array $context = []): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function info(string $message, array $context = []): void;

    /**
     * @param array<string, scalar|null> $context
     */
    public function debug(string $message, array $context = []): void;
}
