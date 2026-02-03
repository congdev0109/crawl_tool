<?php

namespace CrawlTool\Parser;

use CrawlTool\Utils\UrlHelper;

class DataExtractor
{
    public function extract(string $html, string $type, string $baseUrl): array
    {
        $parser = new HtmlParser($html);

        return match ($type) {
            'product_detail', 'product' => $this->extractProduct($parser, $baseUrl),
            'news_detail', 'news' => $this->extractNews($parser, $baseUrl),
            default => [],
        };
    }

    private function extractProduct(HtmlParser $parser, string $baseUrl): array
    {
        // Try common selectors
        $data = [
            'namevi' => $parser->getText('h1.title-product, h1.name-product, .product-detail .name') ?? 'Product Name',
            'code' => $parser->getText('.product-code, .masp') ?? '',
            'regular_price' => $this->parsePrice($parser->getText('.price-old, .regular-price')),
            'sale_price' => $this->parsePrice($parser->getText('.price-new, .sale-price, .price')),
            'descvi' => $parser->getHtml('.desc-pro, .short-description'),
            'contentvi' => $parser->getHtml('.content-pro, .description'),
            'photo' => '',
            'gallery' => '', // TODO: Extract gallery
            'date_created' => time(),
            'status' => 'hienthi',
            'type' => 'san-pham',
        ];

        // Image
        $imgSrc = $parser->getAttribute('.product-image img, .detail-product img', 'src');
        if ($imgSrc) {
            $data['photo'] = basename($imgSrc);
        }

        return $data;
    }

    private function extractNews(HtmlParser $parser, string $baseUrl): array
    {
        $data = [
            'namevi' => $parser->getText('h1.title-news, .article-title') ?? 'News Title',
            'descvi' => $parser->getText('.desc-news, .sapou'),
            'contentvi' => $parser->getHtml('.content-news, .article-content'),
            'photo' => '',
            'date_created' => time(),
            'status' => 'hienthi',
            'type' => 'tintuc',
        ];

        $imgSrc = $parser->getAttribute('.news-image img', 'src');
        if ($imgSrc) {
            $data['photo'] = basename($imgSrc);
        }

        return $data;
    }

    private function parsePrice(?string $priceStr): float
    {
        if (!$priceStr) return 0;
        return (float) preg_replace('/[^0-9]/', '', $priceStr);
    }
}
