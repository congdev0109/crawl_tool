<?php

namespace CrawlTool\Utils;

class UrlHelper
{
    public static function normalize(string $url): string
    {
        // Remove fragment
        $url = strtok($url, '#');
        return rtrim($url, '/');
    }

    public static function getDomain(string $url): string
    {
        return parse_url($url, PHP_URL_HOST);
    }

    public static function resolveUrl(string $base, string $relative): string
    {
        // Clean escaped slashes (common in JS/CSS)
        $relative = str_replace(['\/', '\\'], ['/', '/'], $relative);
        $relative = trim($relative);

        if (empty($relative)) return $base;

        // Clean query params for extension check (temporarily)
        $cleanRelative = strtok($relative, '?');

        // Return if already absolute
        if (parse_url($relative, PHP_URL_SCHEME) != '') {
            return $relative;
        }

        // Handle protocol-relative URLs (//example.com)
        if (str_starts_with($relative, '//')) {
            $scheme = parse_url($base, PHP_URL_SCHEME) ?: 'http';
            return $scheme . ':' . $relative;
        }

        $parts = parse_url($base);
        $root = $parts['scheme'] . '://' . $parts['host'];

        // Handle absolute path (/assets/...)
        if (str_starts_with($relative, '/')) {
            return $root . self::normalizePath($relative);
        }

        // Handle relative path (../fonts/...)
        $path = $parts['path'] ?? '/';
        $baseDir = substr($path, -1) === '/' ? $path : dirname($path);

        $resolvedPath = $baseDir . '/' . $relative;
        return $root . self::normalizePath($resolvedPath);
    }

    private static function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return '/' . implode('/', $absolutes);
    }

    public static function getExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }
}
