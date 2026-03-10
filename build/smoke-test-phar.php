#!/usr/bin/env php
<?php

/**
 * PHAR smoke-test: require the built PHAR and verify autoloading works.
 *
 * Usage: php build/smoke-test-phar.php /path/to/lphenom-log.phar
 */

declare(strict_types=1);

$pharFile = $argv[1] ?? dirname(__DIR__) . '/lphenom-log.phar';

if (!file_exists($pharFile)) {
    fwrite(STDERR, 'PHAR not found: ' . $pharFile . PHP_EOL);
    exit(1);
}

require $pharFile;

// Test NullLogger
$logger = new \LPhenom\Log\Logger\NullLogger('smoke');
$logger->info('smoke test message');
$logger->error('error', ['code' => 42]);
echo 'smoke-test: NullLogger OK' . PHP_EOL;

// Test StdoutLogger output capture
ob_start();
$stdoutLogger = new \LPhenom\Log\Logger\StdoutLogger('test');
$stdoutLogger->warning('stdout warning');
$output = ob_get_clean();
if (!is_string($output) || strpos($output, 'stdout warning') === false) {
    fwrite(STDERR, 'StdoutLogger smoke test FAILED' . PHP_EOL);
    exit(1);
}
echo 'smoke-test: StdoutLogger OK' . PHP_EOL;

// Test LogRecord
$record = new \LPhenom\Log\Contract\LogRecord(
    1700000000.123,
    \LPhenom\Log\Contract\LogLevel::INFO,
    'phar smoke test',
    'phar',
    ['key' => 'value'],
);
if ($record->contextJson() !== '{"key":"value"}') {
    fwrite(STDERR, 'LogRecord::contextJson() smoke test FAILED' . PHP_EOL);
    exit(1);
}
echo 'smoke-test: LogRecord OK' . PHP_EOL;

// Test formatters
$lineFormatter = new \LPhenom\Log\Formatter\LineFormatter();
$jsonFormatter = new \LPhenom\Log\Formatter\JsonFormatter();
$lineOutput    = $lineFormatter->format($record);
$jsonOutput    = $jsonFormatter->format($record);

if (strpos($lineOutput, 'phar smoke test') === false) {
    fwrite(STDERR, 'LineFormatter smoke test FAILED' . PHP_EOL);
    exit(1);
}
echo 'smoke-test: LineFormatter OK' . PHP_EOL;

$decoded = json_decode(trim($jsonOutput), true);
if (!is_array($decoded) || ($decoded['level'] ?? '') !== 'info') {
    fwrite(STDERR, 'JsonFormatter smoke test FAILED' . PHP_EOL);
    exit(1);
}
echo 'smoke-test: JsonFormatter OK' . PHP_EOL;

echo '=== PHAR smoke-test: OK ===' . PHP_EOL;

