<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use CrawlTool\Web\CrawlController;

echo "Testing CrawlController...\n";

try {
    $controller = new CrawlController();
    $input = [
        'url' => 'https://example.com',
        'project_name' => 'test_project',
        'type' => 'full'
    ];

    $result = $controller->handleCrawl($input);
    echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
