# lphenom/log

[![CI](https://github.com/lphenom/log/actions/workflows/ci.yml/badge.svg)](https://github.com/lphenom/log/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP >= 8.1](https://img.shields.io/badge/PHP-%3E%3D8.1-8892BF.svg)](https://www.php.net/)

**KPHP-compatible logging package** for the [LPhenom](https://github.com/lphenom) framework.

> Works in classic PHP (shared hosting / Apache / Nginx) **and** as KPHP-compiled binary — same code, zero changes.

---

## Features

- 📋 **RFC 5424 log levels** as typed constants (`LogLevel::ERROR`, etc.)
- 📦 **Immutable `LogRecord` DTO** — timestamp, level, message, channel, context
- 🖊 **Formatters**: `LineFormatter`, `JsonFormatter`
- 🔌 **Handlers**: `NullHandler`, `StdoutHandler`, `FileHandler` (flock + size rotation), `StackHandler`
- 🪵 **Loggers**: `NullLogger`, `StdoutLogger`, `FileLogger`, `StackLogger`
- 🔒 **KPHP-safe**: no Reflection, no eval, no dynamic class loading, no variable variables
- 📐 Strict types everywhere (`declare(strict_types=1)`)

---

## Requirements

- PHP >= 8.1
- No external runtime dependencies

---

## Installation

```bash
composer require lphenom/log
```

---

## Quick Start

```php
use LPhenom\Log\Logger\StdoutLogger;
use LPhenom\Log\Logger\FileLogger;
use LPhenom\Log\Logger\StackLogger;
use LPhenom\Log\Handler\StdoutHandler;
use LPhenom\Log\Handler\FileHandler;

// Simple stdout logger
$logger = new StdoutLogger(channel: 'app');
$logger->info('Application started');
$logger->error('Something went wrong', ['user_id' => 42]);

// File logger with 10 MiB rotation, keep 5 files
$logger = new FileLogger(
    filePath: '/var/log/app/app.log',
    maxBytes: 10 * 1024 * 1024,
    maxFiles: 5,
    channel: 'app',
);
$logger->warning('Low disk space', ['free_mb' => 512]);

// Stack logger: write to stdout AND file simultaneously
$logger = new StackLogger([
    new StdoutHandler(),
    new FileHandler('/var/log/app/app.log'),
], channel: 'app');

$logger->debug('Request received', ['method' => 'GET', 'path' => '/api/health']);
```

---

## Log Levels

```php
use LPhenom\Log\Contract\LogLevel;

LogLevel::EMERGENCY // 0 — system is unusable
LogLevel::ALERT     // 1 — action must be taken immediately
LogLevel::CRITICAL  // 2 — critical conditions
LogLevel::ERROR     // 3 — error conditions
LogLevel::WARNING   // 4 — warning conditions
LogLevel::NOTICE    // 5 — normal but significant
LogLevel::INFO      // 6 — informational
LogLevel::DEBUG     // 7 — debug-level messages
```

---

## Context Rules

Context keys must be `string`, values must be `scalar|null` (KPHP-compatible):

```php
// ✅ Valid
$logger->info('Login', ['user_id' => 42, 'ip' => '127.0.0.1', 'success' => true]);

// ❌ Invalid — objects not allowed
$logger->info('Login', ['user' => $userObject]);
```

---

## Architecture

```
src/
├── Contract/
│   ├── LogLevel.php          # Level constants + validation
│   ├── LogRecord.php         # Immutable DTO
│   ├── LoggerInterface.php   # Logger contract
│   ├── FormatterInterface.php
│   └── HandlerInterface.php
├── Exception/
│   ├── LogException.php
│   └── InvalidLogLevelException.php
├── Formatter/
│   ├── LineFormatter.php     # Human-readable one-liner
│   └── JsonFormatter.php     # NDJSON / JSON Lines
├── Handler/
│   ├── NullHandler.php
│   ├── StdoutHandler.php
│   ├── FileHandler.php       # flock + size rotation
│   └── StackHandler.php      # fan-out to multiple handlers
└── Logger/
    ├── AbstractLogger.php    # Sugar methods (info/error/debug/…)
    ├── NullLogger.php
    ├── StdoutLogger.php
    ├── FileLogger.php
    └── StackLogger.php
```

---

## Development

```bash
# Build Docker environment
make build

# Install dependencies
make install

# Run tests
make test

# Lint (dry-run)
make lint

# Auto-fix code style
make lint-fix

# PHPStan analysis
make phpstan
```

---

## License

MIT © [LPhenom Contributors](https://github.com/lphenom)
