# lphenom/log — Package Documentation

## Overview

`lphenom/log` is a lightweight, KPHP-compatible logging library for the LPhenom framework.
It provides a clean, explicit API with no magic, no reflection, and full PHP 8.1+ strict typing.

---

## Table of Contents

- [Contracts](#contracts)
- [Log Levels](#log-levels)
- [LogRecord DTO](#logrecord-dto)
- [Formatters](#formatters)
- [Handlers](#handlers)
- [Loggers](#loggers)
- [Context Rules](#context-rules)
- [Extending](#extending)

---

## Contracts

All core interfaces live in `LPhenom\Log\Contract`.

| Interface / Class        | Purpose                             |
|--------------------------|-------------------------------------|
| `LoggerInterface`        | Logger contract with sugar methods  |
| `HandlerInterface`       | Handler contract (`handle()`)       |
| `FormatterInterface`     | Formatter contract (`format()`)     |
| `LogLevel`               | Level constants + validation        |
| `LogRecord`              | Immutable log record DTO            |

---

## Log Levels

Defined as string constants in `LPhenom\Log\Contract\LogLevel`.
Ordered by severity (0 = most severe):

| Constant              | Value         | RFC 5424 |
|-----------------------|---------------|----------|
| `LogLevel::EMERGENCY` | `emergency`   | 0        |
| `LogLevel::ALERT`     | `alert`       | 1        |
| `LogLevel::CRITICAL`  | `critical`    | 2        |
| `LogLevel::ERROR`     | `error`       | 3        |
| `LogLevel::WARNING`   | `warning`     | 4        |
| `LogLevel::NOTICE`    | `notice`      | 5        |
| `LogLevel::INFO`      | `info`        | 6        |
| `LogLevel::DEBUG`     | `debug`       | 7        |

```php
LogLevel::isValid('info');    // true
LogLevel::isValid('verbose'); // false
LogLevel::severityMap();      // ['emergency' => 0, ..., 'debug' => 7]
```

---

## LogRecord DTO

`LPhenom\Log\Contract\LogRecord` is an immutable value object created by the logger.

```php
new LogRecord(
    timestamp: microtime(true),  // float — Unix timestamp with microseconds
    level:     'error',          // string — one of LogLevel constants
    message:   'Something broke',
    channel:   'app',
    context:   ['user_id' => 42],
);

$record->contextJson(); // '{"user_id":42}'
```

---

## Formatters

### LineFormatter

Produces a single human-readable line:

```
[2024-01-15 12:34:56.789] app.ERROR: Something broke {"user_id":42}
```

```php
use LPhenom\Log\Formatter\LineFormatter;

$formatter = new LineFormatter(dateFormat: 'Y-m-d H:i:s.v');
$line = $formatter->format($record);
```

### JsonFormatter

Produces one JSON object per line (NDJSON / JSON Lines):

```json
{"timestamp":1705318496.789,"channel":"app","level":"error","message":"Something broke","context":{"user_id":42}}
```

```php
use LPhenom\Log\Formatter\JsonFormatter;

$formatter = new JsonFormatter();
$line = $formatter->format($record);
```

---

## Handlers

### NullHandler

Discards all records silently. Useful in tests.

```php
use LPhenom\Log\Handler\NullHandler;
$handler = new NullHandler();
```

### StdoutHandler

Writes formatted output to STDOUT.

```php
use LPhenom\Log\Handler\StdoutHandler;
use LPhenom\Log\Formatter\JsonFormatter;

$handler = new StdoutHandler(new JsonFormatter());
```

### FileHandler

Writes to a file with exclusive `flock()` and size-based rotation.

```php
use LPhenom\Log\Handler\FileHandler;

$handler = new FileHandler(
    filePath: '/var/log/app/app.log',
    maxBytes: 10 * 1024 * 1024,  // 10 MiB
    maxFiles: 5,                   // keep app.log.1 … app.log.5
);
```

**Rotation behaviour:**
1. Before each write, the current file size is checked.
2. If `size >= maxBytes`, existing `.1`…`.N` files are shifted up.
3. The current file is renamed to `.1`.
4. The oldest file (`.maxFiles`) is deleted.
5. A fresh file is created for the new write.

### StackHandler

Delegates to multiple handlers in order:

```php
use LPhenom\Log\Handler\StackHandler;

$handler = new StackHandler([
    new StdoutHandler(),
    new FileHandler('/var/log/app/app.log'),
]);

$handler->addHandler($extraHandler);
```

---

## Loggers

All loggers extend `AbstractLogger` which implements `LoggerInterface`.

### NullLogger

```php
$logger = new \LPhenom\Log\Logger\NullLogger('channel');
```

### StdoutLogger

```php
$logger = new \LPhenom\Log\Logger\StdoutLogger('app');
$logger->info('started');
```

### FileLogger

```php
$logger = new \LPhenom\Log\Logger\FileLogger(
    filePath: '/var/log/app.log',
    maxBytes: 5 * 1024 * 1024,
    maxFiles: 3,
    channel:  'app',
);
```

### StackLogger

```php
use LPhenom\Log\Logger\StackLogger;
use LPhenom\Log\Handler\StdoutHandler;
use LPhenom\Log\Handler\FileHandler;

$logger = new StackLogger(
    handlers: [new StdoutHandler(), new FileHandler('/var/log/app.log')],
    channel:  'app',
);
$logger->addHandler($anotherHandler);
```

---

## Context Rules

KPHP requires that all array values are type-uniform. To keep the package KPHP-compatible, context values are restricted to `scalar|null`:

| Allowed              | Not Allowed          |
|----------------------|----------------------|
| `int`, `float`       | objects              |
| `string`             | arrays               |
| `bool`               | resources            |
| `null`               | closures             |

```php
// ✅
$logger->info('action', ['user_id' => 1, 'flag' => true, 'name' => 'Alice']);

// ❌ will not type-check in KPHP
$logger->info('action', ['user' => $userObject]);
```

---

## Extending

### Custom Handler

```php
use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Contract\LogRecord;

final class SlackHandler implements HandlerInterface
{
    public function handle(LogRecord $record): void
    {
        // send $record->message to Slack
    }
}
```

### Custom Formatter

```php
use LPhenom\Log\Contract\FormatterInterface;
use LPhenom\Log\Contract\LogRecord;

final class CsvFormatter implements FormatterInterface
{
    public function format(LogRecord $record): string
    {
        return implode(',', [
            $record->timestamp,
            $record->channel,
            $record->level,
            $record->message,
        ]) . PHP_EOL;
    }
}
```

