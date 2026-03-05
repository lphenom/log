<?php

declare(strict_types=1);

namespace LPhenom\Log\Contract;

/**
 * Immutable log record DTO.
 *
 * Context values must be int|float|string|bool|null (KPHP-compatible, no objects/arrays/mixed).
 * KPHP does not support the `scalar` pseudo-type in generics, so we use the explicit union.
 *
 * @phpstan-type ContextValue int|float|string|bool|null
 * @phpstan-type Context array<string, ContextValue>
 */
final class LogRecord
{
    /**
     * @var array<string, int|float|string|bool|null>
     */
    public readonly array $context;

    /**
     * @param array<string, int|float|string|bool|null> $context
     */
    public function __construct(
        public readonly float $timestamp,
        public readonly string $level,
        public readonly string $message,
        public readonly string $channel,
        array $context = [],
    ) {
        $this->context = $context;
    }

    /**
     * Serialize context to a JSON string.
     * Returns '{}' when context is empty.
     */
    public function contextJson(): string
    {
        if ($this->context === []) {
            return '{}';
        }

        $encoded = json_encode(
            $this->context,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );

        return $encoded;
    }
}
