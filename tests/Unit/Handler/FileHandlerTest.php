<?php

declare(strict_types=1);

namespace LPhenom\Log\Tests\Unit\Handler;

use LPhenom\Log\Contract\LogRecord;
use LPhenom\Log\Handler\FileHandler;
use PHPUnit\Framework\TestCase;

final class FileHandlerTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/lphenom_log_test_' . uniqid('', true);
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->tmpDir);
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function makeRecord(string $message = 'test'): LogRecord
    {
        return new LogRecord(
            timestamp: microtime(true),
            level: 'info',
            message: $message,
            channel: 'app',
        );
    }

    public function testWritesToFile(): void
    {
        $path    = $this->tmpDir . '/app.log';
        $handler = new FileHandler($path);

        $handler->handle($this->makeRecord('hello log'));

        self::assertFileExists($path);
        $contents = file_get_contents($path);
        self::assertIsString($contents);
        self::assertStringContainsString('hello log', $contents);
    }

    public function testCreatesDirectoryAutomatically(): void
    {
        $path    = $this->tmpDir . '/nested/deep/app.log';
        $handler = new FileHandler($path);

        $handler->handle($this->makeRecord('nested'));

        self::assertFileExists($path);
    }

    public function testRotationOccursWhenMaxBytesExceeded(): void
    {
        $path    = $this->tmpDir . '/rotate.log';
        // Set very small maxBytes so rotation triggers after first write
        $handler = new FileHandler($path, maxBytes: 1, maxFiles: 3);

        // First write - creates the file
        $handler->handle($this->makeRecord('first'));
        self::assertFileExists($path);

        // Second write - file >= maxBytes, triggers rotation
        $handler->handle($this->makeRecord('second'));

        self::assertFileExists($path . '.1', 'Rotated file .1 should exist');
        self::assertFileExists($path, 'Current log file should exist after rotation');
    }

    public function testMultipleRotations(): void
    {
        $path    = $this->tmpDir . '/multi.log';
        $handler = new FileHandler($path, maxBytes: 1, maxFiles: 3);

        for ($i = 0; $i < 5; $i++) {
            $handler->handle($this->makeRecord("msg $i"));
        }

        self::assertFileExists($path);
        self::assertFileExists($path . '.1');
        self::assertFileExists($path . '.2');
        self::assertFileExists($path . '.3');
        // .4 should NOT exist (maxFiles = 3)
        self::assertFileDoesNotExist($path . '.4');
    }

    public function testNoRotationWhenMaxBytesIsZero(): void
    {
        $path    = $this->tmpDir . '/norotate.log';
        $handler = new FileHandler($path, maxBytes: 0);

        for ($i = 0; $i < 3; $i++) {
            $handler->handle($this->makeRecord("line $i"));
        }

        self::assertFileExists($path);
        self::assertFileDoesNotExist($path . '.1');
        self::assertSame(3, substr_count(file_get_contents($path) ?: '', 'line'));
    }
}
