<?php

declare(strict_types=1);

namespace LPhenom\Log\Contract;

/**
 * Immutable log record DTO.
 *
 * Context values must be scalar or null (KPHP-compatible).
 */
final class LogRecord
{
    /**
     * @param array<string, scalar|null> $context
     */
    public function __construct(
        public readonly float $timestamp,
        public readonly string $level,
        public readonly string $message,
        public readonly string $channel,
        public readonly array $context = [],
    ) {
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

        $encoded = json_encode($this->context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $encoded !== false ? $encoded : '{}';
    }
}
