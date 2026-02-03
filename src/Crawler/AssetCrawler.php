<?php

namespace CrawlTool\Crawler;

use CrawlTool\Config\CrawlerConfig;
use CrawlTool\Utils\UrlHelper;
use CrawlTool\Utils\Logger;

class AssetCrawler
{
    private HttpClient $http;
    private CrawlerConfig $config;
    private Logger $logger;
    private array $downloadedAssets = [];

    public function __construct(HttpClient $http, CrawlerConfig $config, Logger $logger)
    {
        $this->http = $http;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function crawlAssetsFromHtml(string $html, string $baseUrl): void
    {
        $this->logger->info("Scanning assets from HTML (Batch preparation)...");
        $queue = $this->collectAssetsFromHtml($html, $baseUrl);

        if (empty($queue)) return;

        // Perform Batch Download
        $this->http->downloadBatch($queue, $this->config->getConcurrency() ?? 5);

        // Post-download: Parse CSS for nested assets
        foreach ($queue as $item) {
            if ($item['type'] === 'css' && file_exists($item['path'])) {
                $this->parseCssAssets($item['path'], $item['url']);
            }
        }
    }

    private function collectAssetsFromHtml(string $html, string $baseUrl): array
    {
        $queue = [];

        // 1. Collect CSS
        if (preg_match_all('/<link[^>]+href=["\']([^"\']+\.css[^"\']*)["\']/i', $html, $matches)) {
            foreach ($matches[1] as $url) {
                $this->enqueueAsset($queue, $url, $baseUrl, 'css');
            }
        }

        // 2. Collect JS
        if (preg_match_all('/<script[^>]+src=["\']([^"\']+\.js[^"\']*)["\']/i', $html, $matches)) {
            foreach ($matches[1] as $url) {
                $this->enqueueAsset($queue, $url, $baseUrl, 'js');
            }
        }

        // 3. Images
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+\.(png|jpg|jpeg|svg|ico)[^"\']*)["\']/i', $html, $matches)) {
            foreach ($matches[1] as $url) {
                if ($this->isAssetImage($url)) {
                    $this->enqueueAsset($queue, $url, $baseUrl, 'images');
                }
            }
        }

        return $queue;
    }

    private function enqueueAsset(array &$queue, string $url, string $baseUrl, string $type): void
    {
        $meta = $this->getAssetMeta($url, $baseUrl, $type);
        if ($meta) $queue[] = $meta;
    }

    private function getAssetMeta(string $url, string $baseUrl, string $type): ?array
    {
        $absoluteUrl = UrlHelper::resolveUrl($baseUrl, $url);
        $cleanUrl = strtok($absoluteUrl, '?');
        $cleanUrlOriginal = $cleanUrl;

        if (in_array($cleanUrl, $this->downloadedAssets)) {
            return null;
        }

        $savePath = '';

        // Handle Thumbs: Convert to original URL logic
        if (preg_match('/thumbs\/\d+x\d+x\d+\/(.*)/', $cleanUrl, $matches)) {
            $originalPath = $matches[1];
            $parts = parse_url($cleanUrl);
            $baseUrlRoot = $parts['scheme'] . '://' . $parts['host'];
            $absoluteUrl = $baseUrlRoot . '/' . $originalPath;
            $cleanUrl = $absoluteUrl;
            $savePath = $this->config->getOutputPath() . '/' . $originalPath;

            // Mark both as processed
            $this->downloadedAssets[] = $cleanUrlOriginal;
        } else {
            $filename = basename($cleanUrl);
            $filename = preg_replace('/[^\w\-\.]/', '_', $filename);
            if (empty($filename) || $filename === '.' || $filename === '..') {
                $filename = md5($cleanUrl) . '.' . $type;
            }
            $savePath = $this->config->getOutputPath() . "/assets/$type/$filename";
        }

        if (file_exists($savePath)) {
            $this->downloadedAssets[] = $cleanUrl;
            return null;
        }

        // Mark for download tracking
        $this->downloadedAssets[] = $cleanUrl;

        return [
            'url' => $absoluteUrl,
            'path' => $savePath,
            'type' => $type
        ];
    }

    private function parseCssAssets(string $cssFile, string $cssUrl): void
    {
        if (!file_exists($cssFile)) return;

        $content = file_get_contents($cssFile);
        $cssDirUrl = dirname($cssUrl);
        $queue = [];

        // Regex for url(...) in CSS
        if (preg_match_all('/url\s*\(\s*[\'"]?([^\'"\)]+)[\'"]?\s*\)/i', $content, $matches)) {
            foreach ($matches[1] as $assetUrl) {
                // Skip data URIs
                if (str_starts_with($assetUrl, 'data:')) continue;

                $ext = UrlHelper::getExtension($assetUrl);
                $type = $this->getAssetTypeFromExt($ext);

                if ($type) {
                    $meta = $this->getAssetMeta($assetUrl, $cssDirUrl, $type);
                    if ($meta) $queue[] = $meta;
                }
            }
        }

        if (!empty($queue)) {
            $this->http->downloadBatch($queue, $this->config->getConcurrency() ?? 5);
        }
    }

    private function getAssetTypeFromExt(string $ext): ?string
    {
        return match ($ext) {
            'woff', 'woff2', 'ttf', 'eot', 'otf' => 'fonts',
            'png', 'jpg', 'jpeg', 'gif', 'svg' => 'images',
            default => null,
        };
    }

    private function isAssetImage(string $url): bool
    {
        // Simple heuristic: assets usually have these keywords or are in assets folder, or standard upload folders
        return preg_match('/logo|icon|bg-|background|assets|static|theme|upload|thumbs/i', $url);
    }
}
