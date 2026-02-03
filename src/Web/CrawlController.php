<?php

namespace CrawlTool\Web;

use CrawlTool\Config\CrawlerConfig;
use CrawlTool\Utils\Logger;
use CrawlTool\Crawler\HttpClient;
use CrawlTool\Crawler\AssetCrawler;
use CrawlTool\Crawler\PageCrawler;

class CrawlController
{
    private string $rootDir;

    public function __construct()
    {
        $this->rootDir = dirname(__DIR__, 2);
    }

    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? 'dashboard';

        switch ($action) {
            case 'stream_crawl':
                $this->handleStreamCrawl($_GET);
                break;
            case 'save_check_specs':
                $input = json_decode(file_get_contents('php://input'), true);
                header('Content-Type: application/json');
                echo json_encode($this->saveSpecs($input));
                exit;
            case 'get_specs':
                $projectName = $_GET['project'] ?? '';
                header('Content-Type: application/json');
                echo json_encode($this->getSpecs($projectName));
                exit;
            default:
                include __DIR__ . '/../../templates/layout.php';
                break;
        }
    }

    public function handleCrawl(array $input): array
    {
        $url = $input['url'] ?? '';
        $projectName = $input['project_name'] ?? 'default';

        if (empty($url) || empty($projectName)) {
            return ['status' => 'error', 'message' => 'URL and Project Name are required'];
        }

        // Sanitize Project Name
        $projectName = preg_replace('/[^\w\-]/', '_', $projectName);

        // Setup paths
        $outputDir = $this->rootDir . '/output/' . $projectName;
        $logFile = $outputDir . '/crawl.log';

        // Initialize Components
        $config = new CrawlerConfig(
            outputPath: $outputDir,
            timeout: 30,
            userAgent: CrawlerConfig::DEFAULT_USER_AGENT,
            delay: 500000
        );

        $logger = new Logger($logFile, false);
        $http = new HttpClient($config, $logger);
        $assetCrawler = new AssetCrawler($http, $config, $logger);
        $pageCrawler = new PageCrawler($http, $assetCrawler, $config, $logger);

        try {
            $depth = (int)($input['depth'] ?? 1);
            $useSitemap = !empty($input['use_sitemap']);

            $logger->info("Starting crawl for project: $projectName");

            if ($useSitemap) {
                $sitemapUrl = rtrim($url, '/') . '/sitemap.xml';
                $parser = new \CrawlTool\Parser\SitemapParser($logger);
                $sitemapUrls = $parser->parse($sitemapUrl);
                $result = ['sitemap_crawled' => count($sitemapUrls), 'pages' => []];
                foreach ($sitemapUrls as $pageUrl => $pageType) {
                    $result['pages'][] = $pageCrawler->crawlRecursive($pageUrl, $depth, $pageType);
                }
            } elseif ($depth > 1) {
                $result = $pageCrawler->crawlRecursive($url, $depth);
            } else {
                $result = $pageCrawler->crawlAndAnalyze($url);
            }

            // Specs Analysis
            $analyzer = new \CrawlTool\Analyzer\SpecsAnalyzer($logger);
            $specs = $analyzer->analyze($result);

            file_put_contents($outputDir . '/specs.json', json_encode($specs, JSON_PRETTY_PRINT));
            $logger->success("Specs analysis saved to specs.json");

            return [
                'status' => 'success',
                'project' => $projectName,
                'log_url' => 'output/' . $projectName . '/crawl.log'
            ];
        } catch (\Exception $e) {
            if (isset($logger)) $logger->error("Crawl error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function handleStreamCrawl(array $getParams): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        if (function_exists('apache_setenv')) @apache_setenv('no-gzip', 1);
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        while (ob_get_level() > 0) ob_end_flush();
        ob_implicit_flush(1);

        $input = [
            'url' => $getParams['url'] ?? '',
            'project_name' => $getParams['project_name'] ?? '',
            'type' => $getParams['type'] ?? 'full',
            'depth' => $getParams['depth'] ?? 1,
            'use_sitemap' => true
        ];

        $projectName = preg_replace('/[^\w\-]/', '_', $input['project_name']);
        if (!$projectName) {
            $this->sendSseMessage("Error: Missing project name");
            echo "data: " . json_encode(['status' => 'error']) . "\n\n";
            exit;
        }

        $outputDir = $this->rootDir . '/output/' . $projectName;
        if (!is_dir($outputDir)) mkdir($outputDir, 0777, true);

        $this->sendSseMessage("Starting crawl...");
        $result = $this->handleCrawl($input);

        if ($result['status'] === 'success') {
            $this->sendSseMessage("Crawl finished successfully!");
            $this->sendSseMessage("check_specs:" . $projectName); // Signal to JS
            echo "data: " . json_encode(['status' => 'done', 'project' => $projectName]) . "\n\n";
        } else {
            $this->sendSseMessage("Error: " . $result['message']);
            echo "data: " . json_encode(['status' => 'error']) . "\n\n";
        }
        exit;
    }

    private function sendSseMessage(string $msg): void
    {
        echo "data: " . json_encode(['message' => $msg]) . "\n\n";
        flush();
    }

    public function getSpecs(string $projectName): array
    {
        $specsFile = $this->rootDir . "/output/$projectName/specs.json";

        if ($projectName && file_exists($specsFile)) {
            return json_decode(file_get_contents($specsFile), true) ?? [];
        }
        return ['error' => 'Specs not found'];
    }

    public function saveSpecs(array $input): array
    {
        $project = $input['project'] ?? '';
        $specs = $input['specs'] ?? [];

        if (!$project || empty($specs)) {
            return ['status' => 'error', 'message' => 'Invalid data'];
        }

        $outputDir = $this->rootDir . "/output/$project";
        $specsFile = "$outputDir/specs.json";

        file_put_contents($specsFile, json_encode($specs, JSON_PRETTY_PRINT));

        try {
            $logger = new \CrawlTool\Utils\Logger("$outputDir/gen_config.log");
            $generator = new \CrawlTool\Generator\TypeConfigGenerator($logger);
            $generator->generate($outputDir, $specs);
            return ['status' => 'success'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
