<?php

namespace CrawlTool\Crawler;

use CrawlTool\Config\CrawlerConfig;
use CrawlTool\Utils\UrlHelper;
use CrawlTool\Utils\Logger;
use CrawlTool\Parser\HtmlParser;
use CrawlTool\Parser\TypeDetector;
use CrawlTool\Parser\DataExtractor;
use CrawlTool\Generator\SqlGenerator;

class PageCrawler
{
    private HttpClient $http;
    private AssetCrawler $assetCrawler;
    private CrawlerConfig $config;
    private Logger $logger;
    private TypeDetector $typeDetector;
    private DataExtractor $extractor;

    private array $visited = [];

    public function __construct(
        HttpClient $http,
        AssetCrawler $assetCrawler,
        CrawlerConfig $config,
        Logger $logger
    ) {
        $this->http = $http;
        $this->assetCrawler = $assetCrawler;
        $this->config = $config;
        $this->logger = $logger;
        $this->typeDetector = new TypeDetector();
        $this->extractor = new DataExtractor();
    }

    public function crawlRecursive(string $url, int $depth = 1, ?string $knownType = null): array
    {
        if ($depth <= 0) return [];

        if (!$this->markVisited($url)) return [];

        $this->logger->info("Crawling ($depth left): $url");

        $html = $this->http->get($url);
        if (!$html) return [];

        $result = $this->initResult($url);

        // 1. Detect & Analyze Type
        // If type is known (e.g. from Sitemap), use it. Otherwise detect.
        $type = $this->detectType($url, $html, $knownType);
        $result['type'] = $type;

        // 2. Extract Data (if detail) or Assets
        $this->assetCrawler->crawlAssetsFromHtml($html, $url);

        // Capture Thumb URLs for Specs Analysis
        $result['thumb_urls'] = $this->extractThumbUrls($html);

        $this->extractDetailDataIfNeeded($type, $html, $url, $result);

        // 3. Find Links for Recursion
        $result['children'] = $this->crawlChildren($html, $url, $depth);

        return $result;
    }

    public function crawlAndAnalyze(string $url): array
    {
        // Wrapper for single page crawl if used directly
        return $this->crawlRecursive($url, 1);
    }

    private function markVisited(string $url): bool
    {
        $cleanUrl = UrlHelper::normalize($url);
        if (isset($this->visited[$cleanUrl])) return false;
        $this->visited[$cleanUrl] = true;
        return true;
    }

    private function initResult(string $url): array
    {
        return [
            'url' => $url,
            'assets_crawled' => 0,
            'children' => []
        ];
    }

    private function detectType(string $url, string $html, ?string $knownType): string
    {
        return $knownType ?? $this->typeDetector->detect($url, $html);
    }

    private function extractThumbUrls(string $html): array
    {
        // Matches: thumbs/500x500x1/path.jpg
        if (preg_match_all('/thumbs\/(\d+x\d+x\d+)\/[^"\']+/i', $html, $matches)) {
            return array_unique($matches[0]);
        }

        return [];
    }

    private function extractDetailDataIfNeeded(string $type, string $html, string $url, array &$result): void
    {
        if (!in_array($type, ['product_detail', 'news_detail'])) return;

        $data = $this->extractor->extract($html, $type, $url);
        if (empty($data)) return;

        $sqlGen = new SqlGenerator();
        $table = str_contains($type, 'product') ? 'table_product' : 'table_news';
        $sql = $sqlGen->generateInsert($table, $data);

        // Save SQL to file
        $sqlFile = $this->config->getOutputPath() . "/sql/data.sql";
        $sqlGen->saveToFile($sqlFile, $sql);
        $result['data_extracted'] = true;
    }

    private function crawlChildren(string $html, string $url, int $depth): array
    {
        if ($depth <= 1) return [];

        $crawler = new HtmlParser($html);
        $links = $crawler->getCrawler()->filter('a')->extract(['href']);

        $domain = UrlHelper::getDomain($url);
        $children = [];

        foreach ($links as $link) {
            $absLink = UrlHelper::resolveUrl($url, $link);
            if ($this->shouldSkipLink($absLink, $domain)) continue;

            $childResult = $this->crawlRecursive($absLink, $depth - 1);
            if (!empty($childResult)) {
                $children[] = $childResult;
            }
        }

        return $children;
    }

    private function shouldSkipLink(string $absLink, string $domain): bool
    {
        // Simple internal link check
        if (!str_contains($absLink, $domain)) return true;

        // Skip assets or strange protocols
        return preg_match('/\.(jpg|png|pdf|zip|css|js)$/i', $absLink) === 1;
    }
}
