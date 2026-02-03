<?php

namespace CrawlTool\Analyzer;

use CrawlTool\Utils\Logger;
use CrawlTool\Config\TypePatterns;

class SpecsAnalyzer
{
    private Logger $logger;
    private array $specs = [];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Analyze crawl results to determine config specs
     * @param array $crawlData The result array from CrawlController (nested structure)
     */
    public function analyze(array $crawlData): array
    {
        $this->logger->info("Analyzing specs from crawl data...");

        $pages = $this->flattenPages($crawlData);

        // Strategy: 
        // 1. Identify "Nametypes" present in the site.
        //    We look for pages with types like *_list or static pages.
        //    The slug of these pages defines the nametype.
        // 2. Aggregate specs for each nametype.

        $nametypes = $this->buildNametypeGroups($pages);

        // Now we have the groups (e.g. 'du-an' => Base Product, 'tin-tuc' => Base News)
        // We need to associate Detail pages to these groups to check features/images.
        // Since we don't have parent-child links easily here, we use the Type text again.

        $this->attachDetailPages($pages, $nametypes);

        // Analyze grouped specs
        $this->analyzeGroups($nametypes);

        return $this->specs;
    }

    private function assignToFirstBase(array &$nametypes, string $targetBase, array $page): void
    {
        // Heuristic: Assign detail page to the first group with matching base.
        // If we have 'tin-tuc' (news) and 'dich-vu' (news), how do we know which one?
        // Maybe check URL containment?
        // If URL is /dich-vu-thi-cong -> contains 'dich-vu'.
        $url = $page['url'];

        foreach ($nametypes as $name => $data) {
            if ($data['base'] === $targetBase && str_contains($url, $name)) {
                $nametypes[$name]['pages'][] = $page;
                return;
            }
        }

        // Fallback: just first one
        foreach ($nametypes as $name => $data) {
            if ($data['base'] === $targetBase) {
                $nametypes[$name]['pages'][] = $page;
                return;
            }
        }
    }

    private function buildNametypeGroups(array $pages): array
    {
        $nametypes = [];

        foreach ($pages as $page) {
            $type = $page['type'] ?? 'unknown';
            if ($type === 'unknown' || $type === 'home') continue;

            $slug = $this->getSlug($page['url'] ?? '');
            if (empty($slug)) continue;

            if ($this->isListType($type)) {
                $base = $this->baseFromListType($type);
                if ($base) {
                    $this->addNametypePage($nametypes, $slug, $base, $page);
                }
                continue;
            }

            if ($this->isStaticType($type)) {
                $this->addNametypePage($nametypes, $slug, 'static', $page);
            }
        }

        return $nametypes;
    }

    private function addNametypePage(array &$nametypes, string $name, string $base, array $page): void
    {
        if (!isset($nametypes[$name])) {
            $nametypes[$name] = ['base' => $base, 'pages' => []];
        }
        $nametypes[$name]['pages'][] = $page;
    }

    private function attachDetailPages(array $pages, array &$nametypes): void
    {
        foreach ($pages as $page) {
            $type = $page['type'] ?? '';

            if (str_contains($type, 'project_detail')) {
                $this->assignToFirstBase($nametypes, 'product', $page);
            } elseif (str_contains($type, 'news_detail')) {
                $this->assignToFirstBase($nametypes, 'news', $page);
            } elseif (str_contains($type, 'service_detail')) {
                $this->assignToFirstBase($nametypes, 'news', $page);
            }
        }
    }

    private function analyzeGroups(array $nametypes): void
    {
        foreach ($nametypes as $name => $data) {
            $this->analyzeNametype($name, $data['base'], $data['pages']);
        }
    }

    private function isListType(string $type): bool
    {
        return str_contains($type, '_list');
    }

    private function isStaticType(string $type): bool
    {
        return in_array($type, ['about', 'contact', 'static']);
    }

    private function baseFromListType(string $type): ?string
    {
        if (str_contains($type, 'project')) return 'product';
        if (str_contains($type, 'news')) return 'news';
        if (str_contains($type, 'service')) return 'news';
        return null;
    }

    private function getSlug(string $url): string
    {
        return trim(parse_url($url, PHP_URL_PATH), '/');
    }

    private function analyzeNametype(string $name, string $base, array $pages): void
    {
        $this->specs[$name] = [
            'base' => $base, // Important for file grouping
            'nametype' => $name,
            'has_detail' => false,
            'has_gallery' => false, // NEW: Gallery detection
            'thumb_size' => '100x100x1',
            'width' => 0,
            'height' => 0,
            'features' => [
                'seo' => true,
                'schema' => true,
                'slug' => false,
                'view' => false,
                'images' => false,
                'desc' => false,
                'content' => false,
                'content_cke' => false,
                'copy' => true,       // Usually good to have
                'copy_image' => true, // Usually good to have
                'comment' => false,
            ]
        ];

        // Check details & features
        $maxImagesOnDetail = 0;
        foreach ($pages as $p) {
            if (str_contains($p['type'], 'detail')) {
                $this->specs[$name]['has_detail'] = true;

                // Count images on this detail page (from thumb_urls)
                $imageCount = count($p['thumb_urls'] ?? []);
                if ($imageCount > $maxImagesOnDetail) {
                    $maxImagesOnDetail = $imageCount;
                }
            }
        }

        // If detail page has more than 1 image -> needs gallery
        if ($maxImagesOnDetail > 1) {
            $this->specs[$name]['has_gallery'] = true;
        }

        // Feature Logic:
        if ($this->specs[$name]['has_detail'] || $base === 'news' || $base === 'product' || $base === 'service') {
            $this->specs[$name]['features']['slug'] = true;
            $this->specs[$name]['features']['view'] = true;
            $this->specs[$name]['features']['images'] = true;
            $this->specs[$name]['features']['desc'] = true;
            $this->specs[$name]['features']['content'] = true;
            $this->specs[$name]['features']['content_cke'] = true;
        }

        // Image Analysis (Width/Height)
        $sizes = [];
        foreach ($pages as $p) {
            if (!empty($p['thumb_urls'])) {
                foreach ($p['thumb_urls'] as $tUrl) {
                    if (preg_match('/thumbs\/(\d+)x(\d+)x(\d+)\//', $tUrl, $m)) {
                        $w = (int)$m[1];
                        $h = (int)$m[2];
                        $z = (int)$m[3];

                        // Filter out very small icons (likely UI elements, not content thumbs)
                        if ($w < 50 || $h < 50) continue;

                        $sizes[] = ['w' => $w, 'h' => $h, 'z' => $z, 'str' => "$w" . "x" . "$h" . "x" . "$z"];
                    }
                }
            }
        }

        // Find most common size
        if (!empty($sizes)) {
            $counts = array_count_values(array_column($sizes, 'str'));
            arsort($counts);
            $bestSizeStr = array_key_first($counts);

            foreach ($sizes as $s) {
                if ($s['str'] === $bestSizeStr) {
                    $this->specs[$name]['thumb_size'] = $bestSizeStr;
                    $this->specs[$name]['width'] = $s['w'];
                    $this->specs[$name]['height'] = $s['h'];
                    // Force images true if we found valid sizes
                    $this->specs[$name]['features']['images'] = true;
                    break;
                }
            }
        }

        $this->logger->info("Analyzed '$name': has_detail=" . ($this->specs[$name]['has_detail'] ? 'yes' : 'no') .
            ", has_gallery=" . ($this->specs[$name]['has_gallery'] ? 'yes' : 'no') .
            ", max_images=$maxImagesOnDetail");
    }


    private function flattenPages(array $data): array
    {
        $pages = [];
        if (isset($data['sitemap_crawled'])) {
            // Structure from Sitemap Crawl: ['sitemap_crawled' => N, 'pages' => [ ...results... ]]
            foreach ($data['pages'] as $p) {
                $pages[] = $p;
                if (!empty($p['children'])) {
                    $pages = array_merge($pages, $this->flattenPages(['pages' => $p['children']]));
                }
            }
        } elseif (isset($data['url'])) {
            // Single page recursion root
            $pages[] = $data;
            if (!empty($data['children'])) {
                $pages = array_merge($pages, $this->flattenPages(['pages' => $data['children']]));
            }
        } elseif (isset($data['pages'])) {
            // Just a list
            foreach ($data['pages'] as $p) {
                $pages = array_merge($pages, $this->flattenPages($p));
            }
        }

        return $pages;
    }
}
