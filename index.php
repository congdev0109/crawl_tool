<?php
// public/index.php php -S localhost:8000 server.php

require_once __DIR__ . '/vendor/autoload.php';

use CrawlTool\Web\CrawlController;
use CrawlTool\Web\ProjectManager;

// Basic Routing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

// Initialize ProjectManager
$projectManager = new ProjectManager(__DIR__);

// Handle action-based routes (for AJAX from dashboard)
if ($action) {
    header('Content-Type: application/json');
    $controller = new CrawlController();

    switch ($action) {
        case 'get_specs':
            $projectName = $_GET['project'] ?? '';
            if (empty($projectName)) {
                echo json_encode(['error' => 'Project name required']);
            } else {
                echo json_encode($controller->getSpecs($projectName));
            }
            exit;

        case 'save_check_specs':
            $input = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->saveSpecs($input));
            exit;

        case 'stream_crawl':
            // SSE streaming for crawl progress
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Disable nginx buffering

            // Disable output buffering
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', false);
            while (ob_get_level()) ob_end_flush();

            $projectName = $_GET['project_name'] ?? '';
            $url = $_GET['url'] ?? '';
            $type = $_GET['type'] ?? 'full';
            $depth = (int)($_GET['depth'] ?? 1);

            if (empty($projectName) || empty($url)) {
                echo "data: " . json_encode(['status' => 'error', 'message' => 'Project name and URL required']) . "\n\n";
                flush();
                exit;
            }

            try {
                $controller->handleStreamCrawl([
                    'project_name' => $projectName,
                    'url' => $url,
                    'type' => $type,
                    'depth' => $depth,
                ]);
            } catch (\Throwable $e) {
                echo "data: " . json_encode(['status' => 'error', 'message' => $e->getMessage()]) . "\n\n";
                flush();
            }
            exit;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action: ' . $action]);
            exit;
    }
}

// ============================================
// API Routes
// ============================================

// Stats API
if ($uri === '/api/stats') {
    header('Content-Type: application/json');
    echo json_encode($projectManager->getStats());
    exit;
}

// Projects List API
if ($uri === '/api/projects' && $method === 'GET') {
    header('Content-Type: application/json');
    echo json_encode($projectManager->listProjects());
    exit;
}

// Project Detail API (with optional log)
if (preg_match('#^/api/projects/([^/]+)(/log)?$#', $uri, $matches)) {
    header('Content-Type: application/json');
    $projectName = $matches[1];
    $isLog = isset($matches[2]) && $matches[2] === '/log';
    
    if ($method === 'GET') {
        if ($isLog) {
            echo json_encode(['log' => $projectManager->getProjectLog($projectName)]);
        } else {
            echo json_encode($projectManager->getProjectInfo($projectName));
        }
        exit;
    }
    
    if ($method === 'DELETE') {
        $success = $projectManager->deleteProject($projectName);
        echo json_encode([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Project deleted' : 'Failed to delete project'
        ]);
        exit;
    }
}

// Crawl API
if ($uri === '/api/crawl') {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    header('Content-Type: application/json');

    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        exit;
    }

    try {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON input');
        }

        $controller = new CrawlController();
        echo json_encode($controller->handleCrawl($input));
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// Page Routes
// ============================================

// Serve static files directly
if (is_file(__DIR__ . $uri)) {
    return false;
}

// Home - Dashboard
if ($uri === '/') {
    $currentPage = 'dashboard';
    ob_start();
    include __DIR__ . '/templates/dashboard.php';
    $content = ob_get_clean();
    include __DIR__ . '/templates/layout.php';
    exit;
}

// Projects list
if ($uri === '/projects') {
    $currentPage = 'projects';
    ob_start();
    include __DIR__ . '/templates/projects.php';
    $content = ob_get_clean();
    include __DIR__ . '/templates/layout.php';
    exit;
}

// Logs page
if ($uri === '/logs') {
    $currentPage = 'logs';
    ob_start();
    echo '<div class="pt-3 pb-2 mb-3 border-bottom border-secondary">';
    echo '<h1 class="h2"><i class="bi bi-terminal me-2"></i>System Logs</h1>';
    echo '</div>';
    echo '<div class="alert alert-info">Log viewer coming soon...</div>';
    $content = ob_get_clean();
    include __DIR__ . '/templates/layout.php';
    exit;
}

// Settings page
if ($uri === '/settings') {
    $currentPage = 'settings';
    ob_start();
    echo '<div class="pt-3 pb-2 mb-3 border-bottom border-secondary">';
    echo '<h1 class="h2"><i class="bi bi-gear me-2"></i>Settings</h1>';
    echo '</div>';
    echo '<div class="alert alert-info">Settings page coming soon...</div>';
    $content = ob_get_clean();
    include __DIR__ . '/templates/layout.php';
    exit;
}

// 404
http_response_code(404);
echo "404 Not Found";
