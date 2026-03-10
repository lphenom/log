<?php

declare(strict_types=1);

namespace LPhenom\Log\Contract;

/**
 * PSR-3-inspired logger interface (KPHP-compatible, no magic).
 *
 * Context values MUST be int|float|string|false|null.
 * KPHP does not support `bool` in generics — use `false` instead.
 * Objects, arrays and closures in context are NOT allowed.
 */
interface LoggerInterface
{
    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function log(string $level, string $message, array $context = []): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function alert(string $message, array $context = []): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function critical(string $message, array $context = []): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function error(string $message, array $context = []): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function warning(string $message, array $context = []): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function notice(string $message, array $context = []): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function info(string $message, array $context = []): void;

    /**
     * @param array<string, int|float|string|false|null> $context
     */
    public function debug(string $message, array $context = []): void;
}
