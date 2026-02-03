<?php
// Router for PHP built-in server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

// All requests go to index.php
require __DIR__ . '/index.php';
