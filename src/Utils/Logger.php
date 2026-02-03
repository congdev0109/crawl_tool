<?php

namespace CrawlTool\Utils;

class Logger
{
    private string $logFile;
    private bool $verbose;

    public function __construct(string $logFile = 'output/crawl.log', bool $verbose = true)
    {
        $this->logFile = $logFile;
        $this->verbose = $verbose;
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }

    public function info(string $message): void
    {
        $this->log('INFO', $message);
    }

    public function error(string $message): void
    {
        $this->log('ERROR', $message);
    }

    public function warning(string $message): void
    {
        $this->log('WARNING', $message);
    }

    public function success(string $message): void
    {
        $this->log('SUCCESS', $message);
    }

    private function log(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] [$level] $message";

        // Write to file
        file_put_contents($this->logFile, $formattedMessage . PHP_EOL, FILE_APPEND);

        // Output to console if verbose
        if ($this->verbose) {
            $color = match ($level) {
                'INFO' => "\033[0;37m", // White
                'ERROR' => "\033[0;31m", // Red
                'WARNING' => "\033[0;33m", // Yellow
                'SUCCESS' => "\033[0;32m", // Green
                default => "\033[0m",
            };
            echo $color . $formattedMessage . "\033[0m" . PHP_EOL;
        }
    }
}
