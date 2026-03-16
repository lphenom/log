# lphenom/log — Документация пакета

## Обзор

`lphenom/log` — лёгкая KPHP-совместимая библиотека логирования для фреймворка LPhenom.
Предоставляет чистый, явный API без магии, без Reflection и с полной строгой типизацией PHP 8.1+.

---

## Содержание

- [Контракты](#контракты)
- [Уровни логов](#уровни-логов)
- [DTO LogRecord](#dto-logrecord)
- [Форматтеры](#форматтеры)
- [Обработчики](#обработчики)
- [Логгеры](#логгеры)
- [Правила контекста](#правила-контекста)
- [Расширение](#расширение)

---

## Контракты

Все основные интерфейсы находятся в `LPhenom\Log\Contract`.

| Интерфейс / Класс        | Назначение                                  |
|--------------------------|---------------------------------------------|
| `LoggerInterface`        | Контракт логгера со вспомогательными методами|
| `HandlerInterface`       | Контракт обработчика (`handle()`)           |
| `FormatterInterface`     | Контракт форматтера (`format()`)            |
| `LogLevel`               | Константы уровней + валидация               |
| `LogRecord`              | Иммутабельный DTO записи лога               |

---

## Уровни логов

Определены как строковые константы в `LPhenom\Log\Contract\LogLevel`.
Упорядочены по серьёзности (0 = наиболее критично):

| Константа             | Значение      | RFC 5424 |
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

## DTO LogRecord

`LPhenom\Log\Contract\LogRecord` — иммутабельный объект-значение, создаваемый логгером.

```php
new LogRecord(
    timestamp: microtime(true),   // float — Unix-время с микросекундами
    level:     'error',           // string — одна из констант LogLevel
    message:   'Что-то сломалось',
    channel:   'app',
    context:   ['user_id' => 42],
);

$record->contextJson(); // '{"user_id":42}'
```

---

## Форматтеры

### LineFormatter

Формирует одну человекочитаемую строку:

```
[2024-01-15 12:34:56.789] app.ERROR: Что-то сломалось {"user_id":42}
```

```php
use LPhenom\Log\Formatter\LineFormatter;

$formatter = new LineFormatter(dateFormat: 'Y-m-d H:i:s.v');
$line = $formatter->format($record);
```

### JsonFormatter

Формирует один JSON-объект на строку (NDJSON / JSON Lines):

```json
{"timestamp":1705318496.789,"channel":"app","level":"error","message":"Что-то сломалось","context":{"user_id":42}}
```

```php
use LPhenom\Log\Formatter\JsonFormatter;

$formatter = new JsonFormatter();
$line = $formatter->format($record);
```

---

## Обработчики

### NullHandler

Молча отбрасывает все записи. Удобен в тестах.

```php
use LPhenom\Log\Handler\NullHandler;
$handler = new NullHandler();
```

### StdoutHandler

Записывает форматированный вывод в STDOUT.

```php
use LPhenom\Log\Handler\StdoutHandler;
use LPhenom\Log\Formatter\JsonFormatter;

$handler = new StdoutHandler(new JsonFormatter());
```

### FileHandler

Записывает в файл с эксклюзивным `flock()` и ротацией по размеру.

```php
use LPhenom\Log\Handler\FileHandler;

$handler = new FileHandler(
    filePath: '/var/log/app/app.log',
    maxBytes: 10 * 1024 * 1024,  // 10 МиБ
    maxFiles: 5,                   // хранить app.log.1 … app.log.5
);
```

**Поведение ротации:**
1. Перед каждой записью проверяется текущий размер файла.
2. Если `size >= maxBytes`, существующие файлы `.1`…`.N` сдвигаются на один выше.
3. Текущий файл переименовывается в `.1`.
4. Самый старый файл (`.maxFiles`) удаляется.
5. Создаётся новый файл для записи.

### StackHandler

Делегирует сразу нескольким обработчикам по очереди:

```php
use LPhenom\Log\Handler\StackHandler;

$handler = new StackHandler([
    new StdoutHandler(),
    new FileHandler('/var/log/app/app.log'),
]);

$handler->addHandler($extraHandler);
```

---

## Логгеры

Все логгеры расширяют `AbstractLogger`, который реализует `LoggerInterface`.

### NullLogger

```php
$logger = new \LPhenom\Log\Logger\NullLogger('channel');
```

### StdoutLogger

```php
$logger = new \LPhenom\Log\Logger\StdoutLogger('app');
$logger->info('запущен');
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

## Правила контекста

KPHP требует однородности типов в массивах. Для обеспечения KPHP-совместимости значения контекста ограничены типом `scalar|null`:

| Допустимо            | Недопустимо          |
|----------------------|----------------------|
| `int`, `float`       | объекты              |
| `string`             | массивы              |
| `bool`               | ресурсы              |
| `null`               | замыкания (Closure)  |

```php
// ✅ Корректно
$logger->info('действие', ['user_id' => 1, 'flag' => true, 'name' => 'Alice']);

// ❌ Не пройдёт проверку типов KPHP
$logger->info('действие', ['user' => $userObject]);
```

---

## Расширение

### Собственный обработчик

```php
use LPhenom\Log\Contract\HandlerInterface;
use LPhenom\Log\Contract\LogRecord;

final class SlackHandler implements HandlerInterface
{
    public function handle(LogRecord $record): void
    {
        // отправить $record->message в Slack
    }
}
```

### Собственный форматтер

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
