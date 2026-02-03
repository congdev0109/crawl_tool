<?php

namespace CrawlTool\Parser;

use CrawlTool\Utils\Logger;
use SimpleXMLElement;

class SitemapParser
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function parse(string $url): array
    {
        $this->logger->info("Fetching sitemap: $url");

        try {
            $content = file_get_contents($url);
            if (!$content) {
                $this->logger->error("Failed to fetch sitemap content.");
                return [];
            }

            $xml = new SimpleXMLElement($content);
            $urls = [];

            $currentContextType = null;

            foreach ($xml->url as $urlElement) {
                $loc = (string) $urlElement->loc;

                // Detect simple pattern type
                $detectedType = \CrawlTool\Config\TypePatterns::detectType($loc);

                if ($detectedType) {
                    $urls[$loc] = $detectedType;

                    // Check if this type starts a new context (marker)
                    $config = \CrawlTool\Config\TypePatterns::TYPES[$detectedType] ?? [];
                    if (isset($config['marker_for'])) {
                        $currentContextType = $config['marker_for'];
                    } elseif (!in_array($detectedType, ['about', 'contact', 'home'])) {
                        // Reset context if we hit a known static page that isn't a list?
                        // Or maybe stick to last list. 
                        // For safety, let's keep context unless explicitly changed by another list.
                    }
                } else {
                    // No direct pattern match -> Assign context type if available
                    if ($currentContextType) {
                        $urls[$loc] = $currentContextType;
                    } else {
                        $urls[$loc] = 'unknown'; // Will let TypeDetector refine later
                    }
                }
            }

            $this->logger->success("Found " . count($urls) . " URLs in sitemap.");
            return $urls; // Returns array [url => type]
        } catch (\Exception $e) {
            $this->logger->error("Error parsing sitemap: " . $e->getMessage());
            return [];
        }
    }
}
