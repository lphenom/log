<?php

declare(strict_types=1);

namespace LPhenom\Log\Contract;

/**
 * Immutable log record DTO.
 *
 * Context values must be int|float|string|false|null (KPHP-compatible).
 * KPHP does not support `bool` in generics — use `false` instead.
 *
 * @phpstan-type ContextValue int|float|string|bool|null
 * @phpstan-type Context array<string, int|float|string|bool|null>
 */
final class LogRecord
{
    /** @var float */
    public float $timestamp;

    /** @var string */
    public string $level;

    /** @var string */
    public string $message;

    /** @var string */
    public string $channel;

    /**
     * @var array<string, int|float|string|false|null>
     */
    public array $context;

    /**
     * @param float $timestamp
     * @param string $level
     * @param string $message
     * @param string $channel
     * @param array<string, int|float|string|false|null> $context
     */
    public function __construct(
        float $timestamp,
        string $level,
        string $message,
        string $channel,
        array $context = []
    ) {
        $this->timestamp = $timestamp;
        $this->level     = $level;
        $this->message   = $message;
        $this->channel   = $channel;
        $this->context   = $context;
    }

    /**
     * Serialize context to a JSON string.
     * Returns '{}' when context is empty.
     *
     * KPHP note: JSON_THROW_ON_ERROR, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES
     * are NOT supported in KPHP. Use json_encode with 0 flags and check for false.
     */
    public function contextJson(): string
    {
        if ($this->context === []) {
            return '{}';
        }

        $encoded = json_encode($this->context, 0);

        if ($encoded === false) {
            return '{}';
        }

        return $encoded;
    }
}
