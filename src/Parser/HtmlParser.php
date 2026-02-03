<?php

namespace CrawlTool\Parser;

use Symfony\Component\DomCrawler\Crawler;

class HtmlParser
{
    private Crawler $crawler;

    public function __construct(string $html)
    {
        $this->crawler = new Crawler($html);
    }

    public function getCrawler(): Crawler
    {
        return $this->crawler;
    }

    public function getText(string $selector): ?string
    {
        try {
            return trim($this->crawler->filter($selector)->text());
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function getHtml(string $selector): ?string
    {
        try {
            return trim($this->crawler->filter($selector)->html());
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function getAttribute(string $selector, string $attribute): ?string
    {
        try {
            return trim($this->crawler->filter($selector)->attr($attribute));
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function ensureAbsoluteUrl(string $url, string $baseUrl): string
    {
        // Simple Logic, use UrlHelper instead if available deeply
        if (parse_url($url, PHP_URL_SCHEME) != '') return $url;
        return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
    }
}
