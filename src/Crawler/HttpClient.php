<?php

namespace CrawlTool\Crawler;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use CrawlTool\Config\CrawlerConfig;
use CrawlTool\Utils\Logger;

class HttpClient
{
    private Client $client;
    private CrawlerConfig $config;
    private Logger $logger;

    public function __construct(CrawlerConfig $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        $this->client = new Client([
            'headers' => [
                'User-Agent' => $config->getUserAgent(),
            ],
            'timeout' => $config->getTimeout(),
            'verify' => false, // Skip SSL verification for crawling
            'cookies' => true,
        ]);
    }

    public function get(string $url): ?string
    {
        try {
            $this->applyRateLimit();
            $this->logger->info("GET: $url");

            $response = $this->client->get($url);

            if ($response->getStatusCode() === 200) {
                return (string) $response->getBody();
            }

            $this->logger->warning("HTTP " . $response->getStatusCode() . ": $url");
            return null;
        } catch (RequestException $e) {
            $this->logger->error("Request failed: " . $e->getMessage());
            return null;
        }
    }

    public function download(string $url, string $savePath): bool
    {
        if ($this->config->isDryRun()) {
            $this->logger->info("[DRY RUN] Would download $url to $savePath");
            return true;
        }

        try {
            $this->applyRateLimit();
            $this->logger->info("Downloading: $url");

            $dir = dirname($savePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $this->client->get($url, ['sink' => $savePath]);
            return true;
        } catch (RequestException $e) {
            $this->logger->error("Download failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Download multiple URLs in parallel
     * @param array $items Array of ['url' => ..., 'path' => ...]
     * @param int $concurrency Number of parallel downloads
     */
    public function downloadBatch(array $items, int $concurrency = 5): void
    {
        if (empty($items)) return;

        $requests = function ($items) {
            foreach ($items as $item) {
                yield new Request('GET', $item['url']);
            }
        };

        $pool = new Pool($this->client, $requests($items), [
            'concurrency' => $concurrency,
            'fulfilled' => function ($response, $index) use ($items) {
                $item = $items[$index];
                $savePath = $item['path'];

                $dir = dirname($savePath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                file_put_contents($savePath, $response->getBody());
                $this->logger->success("Downloaded (Multi): " . $item['url']);
            },
            'rejected' => function ($reason, $index) use ($items) {
                $item = $items[$index];
                $this->logger->error("Failed (Multi): " . $item['url'] . " - Reason: " . $reason->getMessage());
            },
        ]);

        // Initiate the transfers and wait for the pool to finish
        $promise = $pool->promise();
        $promise->wait();
    }

    private function applyRateLimit(): void
    {
        usleep($this->config->getDelay());
    }
}
