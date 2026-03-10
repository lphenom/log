<?php

/**
 * KPHP entrypoint for lphenom/log.
 *
 * KPHP does NOT support Composer PSR-4 autoloading.
 * All files must be included explicitly via require_once in dependency order:
 *   Exceptions → Interfaces → Value Objects → Concrete classes
 *
 * This file is used by Dockerfile.check Stage 1 (kphp-build) to verify
 * that all source files compile cleanly under vkcom/kphp.
 */

declare(strict_types=1);

// --- Exceptions (no dependencies) ---
require_once __DIR__ . '/../src/Exception/LogException.php';
require_once __DIR__ . '/../src/Exception/InvalidLogLevelException.php';

// --- Contracts (interfaces + value objects) ---
require_once __DIR__ . '/../src/Contract/LogLevel.php';
require_once __DIR__ . '/../src/Contract/LogRecord.php';
require_once __DIR__ . '/../src/Contract/FormatterInterface.php';
require_once __DIR__ . '/../src/Contract/HandlerInterface.php';
require_once __DIR__ . '/../src/Contract/LoggerInterface.php';

// --- Formatters (depend on contracts) ---
require_once __DIR__ . '/../src/Formatter/LineFormatter.php';
require_once __DIR__ . '/../src/Formatter/JsonFormatter.php';

// --- Handlers (depend on contracts + formatters) ---
require_once __DIR__ . '/../src/Handler/NullHandler.php';
require_once __DIR__ . '/../src/Handler/StdoutHandler.php';
require_once __DIR__ . '/../src/Handler/FileHandler.php';
require_once __DIR__ . '/../src/Handler/StackHandler.php';

// --- Loggers (depend on contracts + handlers) ---
require_once __DIR__ . '/../src/Logger/AbstractLogger.php';
require_once __DIR__ . '/../src/Logger/NullLogger.php';
require_once __DIR__ . '/../src/Logger/StdoutLogger.php';
require_once __DIR__ . '/../src/Logger/FileLogger.php';
require_once __DIR__ . '/../src/Logger/StackLogger.php';

// --- Smoke test: verify basic usage compiles and runs ---

$nullLogger = new \LPhenom\Log\Logger\NullLogger('app');
$nullLogger->info('kphp-entrypoint: NullLogger OK');
$nullLogger->error('error test', ['code' => 42]);

$stdoutLogger = new \LPhenom\Log\Logger\StdoutLogger('kphp');
$stdoutLogger->info('kphp-entrypoint: StdoutLogger OK');

$stackLogger = new \LPhenom\Log\Logger\StackLogger([], 'stack');
$stackLogger->addHandler(new \LPhenom\Log\Handler\NullHandler());
$stackLogger->debug('kphp-entrypoint: StackLogger OK');

$record = new \LPhenom\Log\Contract\LogRecord(
    microtime(true),
    \LPhenom\Log\Contract\LogLevel::INFO,
    'kphp-entrypoint: LogRecord OK',
    'test',
    ['key' => 'value', 'num' => 1]
);

$lineFormatter = new \LPhenom\Log\Formatter\LineFormatter();
$jsonFormatter = new \LPhenom\Log\Formatter\JsonFormatter();

$lineOutput = $lineFormatter->format($record);
$jsonOutput = $jsonFormatter->format($record);

echo $lineOutput;
echo $jsonOutput;
echo '=== kphp-entrypoint: ALL OK ===' . PHP_EOL;


