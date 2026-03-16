# lphenom/log

[![CI](https://github.com/lphenom/log/actions/workflows/ci.yml/badge.svg)](https://github.com/lphenom/log/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP >= 8.1](https://img.shields.io/badge/PHP-%3E%3D8.1-8892BF.svg)](https://www.php.net/)

**KPHP-совместимый пакет логирования** для фреймворка [LPhenom](https://github.com/lphenom).

> Работает как в классическом PHP (shared hosting / Apache / Nginx), **так и** в виде KPHP-скомпилированного бинарника — один и тот же код, без изменений.

---

## Возможности

- 📋 **Уровни логов RFC 5424** как типизированные константы (`LogLevel::ERROR` и т.д.)
- 📦 **Иммутабельный DTO `LogRecord`** — timestamp, level, message, channel, context
- 🖊 **Форматтеры**: `LineFormatter`, `JsonFormatter`
- 🔌 **Обработчики**: `NullHandler`, `StdoutHandler`, `FileHandler` (flock + ротация по размеру), `StackHandler`
- 🪵 **Логгеры**: `NullLogger`, `StdoutLogger`, `FileLogger`, `StackLogger`
- 🔒 **KPHP-безопасен**: нет Reflection, eval, динамической загрузки классов, переменных переменных
- 📐 Строгая типизация везде (`declare(strict_types=1)`)

---

## Требования

- PHP >= 8.1
- Нет внешних runtime-зависимостей

---

## Установка

```bash
composer require lphenom/log:^0.1
```

---

## Быстрый старт

```php
use LPhenom\Log\Logger\StdoutLogger;
use LPhenom\Log\Logger\FileLogger;
use LPhenom\Log\Logger\StackLogger;
use LPhenom\Log\Handler\StdoutHandler;
use LPhenom\Log\Handler\FileHandler;

// Простой логгер в stdout
$logger = new StdoutLogger(channel: 'app');
$logger->info('Приложение запущено');
$logger->error('Что-то пошло не так', ['user_id' => 42]);

// Файловый логгер с ротацией 10 МиБ, хранить 5 файлов
$logger = new FileLogger(
    filePath: '/var/log/app/app.log',
    maxBytes: 10 * 1024 * 1024,
    maxFiles: 5,
    channel: 'app',
);
$logger->warning('Мало места на диске', ['free_mb' => 512]);

// Стек-логгер: писать одновременно в stdout И файл
$logger = new StackLogger([
    new StdoutHandler(),
    new FileHandler('/var/log/app/app.log'),
], channel: 'app');

$logger->debug('Запрос получен', ['method' => 'GET', 'path' => '/api/health']);
```

---

## Уровни логов

```php
use LPhenom\Log\Contract\LogLevel;

LogLevel::EMERGENCY // 0 — система неработоспособна
LogLevel::ALERT     // 1 — требуется немедленное действие
LogLevel::CRITICAL  // 2 — критические условия
LogLevel::ERROR     // 3 — условия ошибки
LogLevel::WARNING   // 4 — предупреждения
LogLevel::NOTICE    // 5 — нормально, но значимо
LogLevel::INFO      // 6 — информационные сообщения
LogLevel::DEBUG     // 7 — отладочные сообщения
```

---

## Правила контекста

Ключи контекста — `string`, значения — `scalar|null` (совместимо с KPHP):

```php
// ✅ Корректно
$logger->info('Вход', ['user_id' => 42, 'ip' => '127.0.0.1', 'success' => true]);

// ❌ Некорректно — объекты недопустимы
$logger->info('Вход', ['user' => $userObject]);
```

---

## Архитектура

```
src/
├── Contract/
│   ├── LogLevel.php          # Константы уровней + валидация
│   ├── LogRecord.php         # Иммутабельный DTO
│   ├── LoggerInterface.php   # Контракт логгера
│   ├── FormatterInterface.php
│   └── HandlerInterface.php
├── Exception/
│   ├── LogException.php
│   └── InvalidLogLevelException.php
├── Formatter/
│   ├── LineFormatter.php     # Человекочитаемая строка
│   └── JsonFormatter.php     # NDJSON / JSON Lines
├── Handler/
│   ├── NullHandler.php
│   ├── StdoutHandler.php
│   ├── FileHandler.php       # flock + ротация по размеру
│   └── StackHandler.php      # Fan-out на несколько обработчиков
└── Logger/
    ├── AbstractLogger.php    # Sugar-методы (info/error/debug/…)
    ├── NullLogger.php
    ├── StdoutLogger.php
    ├── FileLogger.php
    └── StackLogger.php
```

---

## Разработка

```bash
# Собрать Docker-окружение
make build

# Установить зависимости
make install

# Запустить тесты
make test

# Линтер (dry-run)
make lint

# Автоисправление стиля кода
make lint-fix

# Анализ PHPStan
make phpstan
```

---

## Лицензия

MIT © [LPhenom Contributors](https://github.com/lphenom)
