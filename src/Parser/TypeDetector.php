<?php

namespace CrawlTool\Parser;

use CrawlTool\Config\TypePatterns;

class TypeDetector
{
    public function detect(string $url, string $html): string
    {
        // 1. Try URL pattern
        $type = TypePatterns::detectType($url, $html);
        if ($type) {
            return $type;
        }

        // 2. Try HTML structure (Fallback)
        $parser = new HtmlParser($html);
        $crawler = $parser->getCrawler();

        if ($crawler->filter('.product-detail, .detail-product')->count() > 0) {
            return 'product_detail';
        }

        if ($crawler->filter('.list-product, .product-item')->count() > 0) {
            return 'product_list';
        }

        if ($crawler->filter('.news-detail, .article-content')->count() > 0) {
            return 'news_detail';
        }

        return 'other'; // Unknown
    }
}
