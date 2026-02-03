<?php

namespace CrawlTool\Config;

class TypePatterns
{
    public const TYPES = [
        'home' => [
            'pattern' => '/^$/', // Root domain
            'priority' => 100,
        ],
        // STATIC PAGES
        'about' => [
            'pattern' => '/^gioi-thieu$/i',
            'table' => 'table_static',
        ],
        'contact' => [
            'pattern' => '/^lien-he$/i',
            'table' => 'table_static', // or custom contact
        ],

        // PROJECT / PRODUCT
        'project_list' => [
            'pattern' => '/^du-an$/i',
            'marker_for' => 'project_detail', // If we see this, following items are project_detail
            'table' => 'table_product_list',
        ],
        'project_detail' => [
            // No pattern, detected by context or HTML
            'html_selectors' => ['.product-detail', '.detail-product', '.chi-tiet-du-an'],
            'table' => 'table_product',
        ],

        // NEWS
        'news_list' => [
            'pattern' => '/^tin-tuc$/i',
            'marker_for' => 'news_detail',
            'table' => 'table_news_list',
        ],
        'news_detail' => [
            'html_selectors' => ['.news-detail', '.detail-news', '.chi-tiet-tin-tuc'],
            'table' => 'table_news',
        ],

        // SERVICE
        'service_list' => [
            'pattern' => '/^dich-vu$/i',
            'marker_for' => 'service_detail',
            'table' => 'table_service_list',
        ],
        'service_detail' => [
            'html_selectors' => ['.news-detail'], // Services often use news detail structure
            'table' => 'table_news',
        ],

        // OTHERS (Video, Gallery etc can be added)
    ];

    public static function detectType(string $url, string $html = ''): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = trim($path ?? '', '/');

        if ($path === '') return 'home';

        // Check patterns first
        foreach (self::TYPES as $type => $config) {
            if (isset($config['pattern']) && preg_match($config['pattern'], $path)) {
                return $type;
            }
        }

        return null; // Fallback to HTML analysis in TypeDetector
    }
}
