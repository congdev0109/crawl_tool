<?php

require_once __DIR__ . '/vendor/autoload.php';

use CrawlTool\Web\CrawlController;
use CrawlTool\Utils\Logger;

// Basic CLI Router
$args = $argv;
array_shift($args); // remove script name

// Parse arguments
$url = '';
$params = [];
foreach ($args as $arg) {
    if (strpos($arg, '--') === 0) {
        $parts = explode('=', $arg, 2);
        $key = ltrim($parts[0], '--');
        $value = $parts[1] ?? true;
        $params[$key] = $value;
    } else {
        if (empty($url)) {
            $url = $arg;
        }
    }
}

if (empty($url) && !isset($params['project']) && !in_array($args[0] ?? '', ['gen_config', 'analyze_photos'])) {
    echo "Usage: php crawl.php [url] --project=[name] [--use-sitemap] [--depth=N]\n";
    echo "       php crawl.php gen_config --project=[name]\n";
    echo "       php crawl.php analyze_photos [url] --project=[name]\n";
    exit(1);
}

// CLI Commands Logic
$command = $url; // First arg might be command or URL

// Analyze Photos Command
if ($command === 'analyze_photos') {
    $project = $params['project'] ?? '';
    $targetUrl = $args[1] ?? ''; // Second arg is URL

    if (!$project || !$targetUrl) die("Usage: php crawl.php analyze_photos [url] --project=[name]\n");

    $outputDir = __DIR__ . "/output/$project";
    if (!is_dir($outputDir)) mkdir($outputDir, 0777, true);

    $logger = new Logger("$outputDir/photo_analysis.log");
    $config = new \CrawlTool\Config\CrawlerConfig(outputPath: $outputDir);
    $http = new \CrawlTool\Crawler\HttpClient($config, $logger);

    $logger->info("Fetching homepage for photo analysis: $targetUrl");
    $html = $http->get($targetUrl);

    if (!$html) die("Failed to fetch URL\n");

    $detector = new \CrawlTool\Analyzer\PhotoDetector($logger);
    $photoSpecs = $detector->analyze([['html' => $html]]);

    file_put_contents("$outputDir/photo_specs.json", json_encode($photoSpecs, JSON_PRETTY_PRINT));
    $logger->success("Photo specs saved to photo_specs.json");

    echo "Photo analysis completed.\n";
    print_r($photoSpecs);
    exit;
}

if ($command === 'gen_config') {
    $project = $params['project'] ?? '';
    if (!$project) die("Project name required for config generation.\n");

    $specsFile = __DIR__ . "/output/$project/specs.json";
    if (!file_exists($specsFile)) die("Specs file not found: $specsFile\n");

    $specs = json_decode(file_get_contents($specsFile), true);
    $logger = new Logger(__DIR__ . "/output/$project/gen_config.log");

    // Load photo specs if available
    $photoSpecsFile = __DIR__ . "/output/$project/photo_specs.json";
    $photoSpecs = [];
    if (file_exists($photoSpecsFile)) {
        $photoSpecs = json_decode(file_get_contents($photoSpecsFile), true);
    }

    $generator = new \CrawlTool\Generator\TypeConfigGenerator($logger);
    $generator->generate(__DIR__ . "/output/$project", $specs, $photoSpecs);

    echo "Config generation completed.\n";
    exit;
}

// Generate Router Command
if ($command === 'gen_router') {
    $project = $params['project'] ?? '';
    if (!$project) die("Project name required for router generation.\n");

    $specsFile = __DIR__ . "/output/$project/specs.json";
    if (!file_exists($specsFile)) die("Specs file not found: $specsFile\n");

    $specs = json_decode(file_get_contents($specsFile), true);
    $logger = new Logger(__DIR__ . "/output/$project/gen_router.log");

    $generator = new \CrawlTool\Generator\RouterGenerator($logger);
    $generator->generate(__DIR__ . "/output/$project", $specs);

    echo "Router generation completed.\n";
    exit;
}

// Generate Sources Command
if ($command === 'gen_sources') {
    $project = $params['project'] ?? '';
    if (!$project) die("Project name required for sources generation.\n");

    $specsFile = __DIR__ . "/output/$project/specs.json";
    if (!file_exists($specsFile)) die("Specs file not found: $specsFile\n");

    $specs = json_decode(file_get_contents($specsFile), true);
    $logger = new Logger(__DIR__ . "/output/$project/gen_sources.log");

    $generator = new \CrawlTool\Generator\SourcesGenerator($logger);
    $generator->generate(__DIR__ . "/output/$project", $specs);

    echo "Sources generation completed.\n";
    exit;
}

// Generate Templates Command
if ($command === 'gen_templates') {
    $project = $params['project'] ?? '';
    if (!$project) die("Project name required for templates generation.\n");

    $specsFile = __DIR__ . "/output/$project/specs.json";
    if (!file_exists($specsFile)) die("Specs file not found: $specsFile\n");

    $specs = json_decode(file_get_contents($specsFile), true);

    // Load photo specs
    $photoSpecsFile = __DIR__ . "/output/$project/photo_specs.json";
    $photoSpecs = [];
    if (file_exists($photoSpecsFile)) {
        $photoSpecs = json_decode(file_get_contents($photoSpecsFile), true);
    }

    $logger = new Logger(__DIR__ . "/output/$project/gen_templates.log");

    $generator = new \CrawlTool\Generator\TemplateGenerator($logger);
    $generator->generate(__DIR__ . "/output/$project", $specs, $photoSpecs);

    echo "Templates generation completed.\n";
    exit;
}

// Default: Crawl
$input = [
    'url' => $url,
    'project_name' => $params['project'] ?? 'cli_crawl_' . time(),
    'depth' => $params['depth'] ?? 1,
    'use_sitemap' => isset($params['use-sitemap']),
];

echo "Starting CLI Crawl...\n";
echo "URL: " . $input['url'] . "\n";
echo "Project: " . $input['project_name'] . "\n";
echo "Sitemap: " . ($input['use_sitemap'] ? 'Yes' : 'No') . "\n";

$controller = new CrawlController();
$result = $controller->handleCrawl($input);

if ($result['status'] === 'success') {
    echo "\n[SUCCESS] Crawl finished!\n";
    echo "Log: " . $result['log_url'] . "\n";
    if (isset($result['specs_url'])) {
        echo "Specs: " . $result['specs_url'] . "\n";
    }
} else {
    echo "\n[ERROR] " . $result['message'] . "\n";
    exit(1);
}
