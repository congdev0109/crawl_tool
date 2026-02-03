<?php

declare(strict_types=1);

namespace CrawlTool\Analyzer;

use CrawlTool\Utils\Logger;

/**
 * Detects photo-related elements from HTML pages
 * Outputs config for: logo, favicon, banner, slideshow, social, partners
 */
class PhotoDetector
{
    private Logger $logger;
    private array $detected = [];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function analyze(array $pages): array
    {
        $this->detected = [
            'photo_static' => [],
            'man_photo' => []
        ];

        foreach ($pages as $page) {
            $html = $page['html'] ?? '';
            if (empty($html)) continue;

            $this->detectLogo($html);
            $this->detectFavicon($html);
            $this->detectBanner($html);
            $this->detectSlideshow($html);
            $this->detectSocial($html);
            $this->detectPartners($html);
        }

        return $this->detected;
    }

    private function detectLogo(string $html): void
    {
        // Pattern 1: img with class/id containing "logo"
        // Pattern 2: img with alt or title containing "logo"
        $logoPatterns = [
            '/<img[^>]+(?:class|id)=["\'][^"\']*logo[^"\']*["\'][^>]*src=["\']([^"\']+)["\']/i',
            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*(?:class|id)=["\'][^"\']*logo/i',
            '/<img[^>]+alt=["\'][^"\']*logo[^"\']*["\'][^>]*src=["\']([^"\']+)["\']/i',
            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*alt=["\'][^"\']*logo/i',
            '/<img[^>]+title=["\'][^"\']*logo[^"\']*["\'][^>]*src=["\']([^"\']+)["\']/i',
            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*title=["\'][^"\']*logo/i',
        ];

        foreach ($logoPatterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                $src = $m[1];
                if (!isset($this->detected['photo_static']['logo'])) {
                    $w = 120;
                    $h = 100;
                    if (preg_match('/thumbs\/(\d+)x(\d+)x\d+\//', $src, $dim)) {
                        $w = (int)$dim[1];
                        $h = (int)$dim[2];
                    }
                    $this->detected['photo_static']['logo'] = [
                        'title' => 'Logo',
                        'width' => $w,
                        'height' => $h
                    ];
                    $this->logger->info("Detected: Logo ({$w}x{$h})");
                    return;
                }
            }
        }
    }

    private function detectFavicon(string $html): void
    {
        // <link rel="icon" or rel="shortcut icon"
        if (preg_match('/<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]*>/i', $html)) {
            if (!isset($this->detected['photo_static']['favicon'])) {
                $this->detected['photo_static']['favicon'] = [
                    'title' => 'Favicon',
                    'width' => 48,
                    'height' => 48
                ];
                $this->logger->info("Detected: Favicon");
            }
        }
    }

    private function detectBanner(string $html): void
    {
        // Look for img in header or with banner class
        if (preg_match('/<(?:div|section|header)[^>]*(?:class|id)=["\'][^"\']*banner[^"\']*["\'][^>]*>.*?<img[^>]+src=["\']([^"\']+)["\']|<img[^>]+(?:class|id)=["\'][^"\']*banner[^"\']*["\'][^>]+src=["\']([^"\']+)["\']/is', $html, $m)) {
            $src = $m[1] ?: ($m[2] ?? '');
            if ($src && !isset($this->detected['photo_static']['banner'])) {
                $w = 730;
                $h = 120;
                if (preg_match('/thumbs\/(\d+)x(\d+)x\d+\//', $src, $dim)) {
                    $w = (int)$dim[1];
                    $h = (int)$dim[2];
                }
                $this->detected['photo_static']['banner'] = [
                    'title' => 'Banner',
                    'width' => $w,
                    'height' => $h
                ];
                $this->logger->info("Detected: Banner ({$w}x{$h})");
            }
        }
    }

    private function detectSlideshow(string $html): void
    {
        // Look for carousel/slider/swiper patterns
        $slidePatterns = [
            'carousel',
            'slider',
            'swiper',
            'slick',
            'owl-carousel',
            'slideshow'
        ];

        foreach ($slidePatterns as $pattern) {
            if (stripos($html, $pattern) !== false) {
                // Try to find slide images
                if (preg_match_all('/<(?:div|section)[^>]*(?:class|id)=["\'][^"\']*' . $pattern . '[^"\']*["\'][^>]*>.*?<img[^>]+src=["\']([^"\']+)["\']/is', $html, $m)) {
                    if (!isset($this->detected['man_photo']['slide'])) {
                        $w = 1366;
                        $h = 600;
                        // Get first image dimensions
                        if (!empty($m[1][0]) && preg_match('/thumbs\/(\d+)x(\d+)x\d+\//', $m[1][0], $dim)) {
                            $w = (int)$dim[1];
                            $h = (int)$dim[2];
                        }
                        $this->detected['man_photo']['slide'] = [
                            'title' => 'Slideshow',
                            'width' => $w,
                            'height' => $h,
                            'count' => count($m[1])
                        ];
                        $this->logger->info("Detected: Slideshow ({$w}x{$h}), " . count($m[1]) . " slides");
                    }
                    break;
                }
            }
        }
    }

    private function detectSocial(string $html): void
    {
        // Pattern 1: Look for div/ul with class containing "social"
        if (preg_match('/<(?:div|ul|section)[^>]*class=["\'][^"\']*social[^"\']*["\'][^>]*>(.*?)<\/(?:div|ul|section)>/is', $html, $socialBlock)) {
            $blockHtml = $socialBlock[1];
            // Count social media links inside the block
            $socialPatterns = ['facebook', 'twitter', 'youtube', 'instagram', 'linkedin', 'tiktok', 'zalo', 'pinterest'];
            $foundCount = 0;
            $foundWith = 30;
            $foundHeight = 30;

            foreach ($socialPatterns as $social) {
                if (preg_match('/<a[^>]+href=["\'][^"\']*' . $social . '[^"\']*["\']/i', $blockHtml)) {
                    $foundCount++;
                }
            }

            // Try to get icon dimensions
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $blockHtml, $imgMatch)) {
                if (preg_match('/thumbs\/(\d+)x(\d+)x\d+\//', $imgMatch[1], $dim)) {
                    $foundWith = (int)$dim[1];
                    $foundHeight = (int)$dim[2];
                }
            }

            if ($foundCount > 0 && !isset($this->detected['man_photo']['social'])) {
                $this->detected['man_photo']['social'] = [
                    'title' => 'Social',
                    'width' => $foundWith,
                    'height' => $foundHeight,
                    'count' => $foundCount
                ];
                $this->logger->info("Detected: Social (via class) - $foundCount platforms");
                return;
            }
        }

        // Fallback: Look for social media links anywhere (old method)
        $socialPatterns = ['facebook', 'twitter', 'youtube', 'instagram', 'linkedin', 'tiktok', 'zalo'];
        $foundCount = 0;

        foreach ($socialPatterns as $social) {
            if (preg_match('/<a[^>]+href=["\'][^"\']*' . $social . '[^"\']*["\']/i', $html)) {
                $foundCount++;
            }
        }

        if ($foundCount >= 2 && !isset($this->detected['man_photo']['social'])) {
            $this->detected['man_photo']['social'] = [
                'title' => 'Social',
                'width' => 30,
                'height' => 30,
                'count' => $foundCount
            ];
            $this->logger->info("Detected: Social links ($foundCount platforms)");
        }
    }

    private function detectPartners(string $html): void
    {
        // Look for partner/client sections
        $partnerPatterns = ['partner', 'doitac', 'doi-tac', 'client', 'khach-hang', 'brand'];

        foreach ($partnerPatterns as $pattern) {
            if (preg_match('/<(?:div|section)[^>]*(?:class|id)=["\'][^"\']*' . $pattern . '[^"\']*["\'][^>]*>.*?(<img[^>]+>.*?)+/is', $html, $m)) {
                if (!isset($this->detected['man_photo']['doitac'])) {
                    // Count partner images
                    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $m[0], $imgs);
                    $count = count($imgs[1]);
                    if ($count > 0) {
                        $w = 175;
                        $h = 95;
                        if (preg_match('/thumbs\/(\d+)x(\d+)x\d+\//', $imgs[1][0], $dim)) {
                            $w = (int)$dim[1];
                            $h = (int)$dim[2];
                        }
                        $this->detected['man_photo']['doitac'] = [
                            'title' => 'Đối tác',
                            'width' => $w,
                            'height' => $h,
                            'count' => $count
                        ];
                        $this->logger->info("Detected: Partners ({$w}x{$h}), $count items");
                    }
                }
                break;
            }
        }
    }

    public function getDetected(): array
    {
        return $this->detected;
    }
}
