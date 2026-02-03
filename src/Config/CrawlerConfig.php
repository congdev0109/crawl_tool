<?php

namespace CrawlTool\Config;

class CrawlerConfig
{
    public const DEFAULT_TIMEOUT = 30;
    public const DEFAULT_USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    public const DEFAULT_DELAY = 1000000; // 1 second in microseconds

    private string $outputPath;
    private int $timeout;
    private string $userAgent;
    private int $delay;
    private int $concurrency;
    private bool $dryRun;

    public function __construct(
        string $outputPath = 'output',
        int $timeout = self::DEFAULT_TIMEOUT,
        string $userAgent = self::DEFAULT_USER_AGENT,
        int $delay = self::DEFAULT_DELAY,
        int $concurrency = 5,
        bool $dryRun = false
    ) {
        $this->outputPath = $outputPath;
        $this->timeout = $timeout;
        $this->userAgent = $userAgent;
        $this->delay = $delay;
        $this->concurrency = $concurrency;
        $this->dryRun = $dryRun;
    }

    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function getConcurrency(): int
    {
        return $this->concurrency;
    }

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    public function setOutputPath(string $outputPath): self
    {
        $this->outputPath = $outputPath;
        return $this;
    }
}
