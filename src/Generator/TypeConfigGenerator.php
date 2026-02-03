<?php

namespace CrawlTool\Generator;

use CrawlTool\Utils\Logger;

class TypeConfigGenerator
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function generate(string $outputDir, array $specs, array $photoSpecs = []): void
    {
        $typeDir = $outputDir . '/config/type';
        if (!is_dir($typeDir)) {
            mkdir($typeDir, 0777, true);
        }

        // Group by base type
        $grouped = [];
        foreach ($specs as $name => $config) {
            $base = $config['base'] ?? 'other';
            $grouped[$base][$name] = $config;
        }

        foreach ($grouped as $base => $nametypes) {
            if ($base === 'other') continue;

            $content = "<?php\n";

            foreach ($nametypes as $name => $spec) {
                // If base is static, $name is like 'gioi-thieu'.
                $content .= $this->buildConfigBlock($base, $name, $spec);
            }

            $filename = "config-type-{$base}.php";
            file_put_contents("$typeDir/$filename", $content);
            $this->logger->info("Generated config for base '$base': $filename");
        }

        // Generate photo config if we have photo specs
        if (!empty($photoSpecs)) {
            $this->generatePhotoConfig($typeDir, $photoSpecs);
        }
    }

    private function generatePhotoConfig(string $typeDir, array $photoSpecs): void
    {
        $content = "<?php\n";

        // photo_static items (logo, favicon, banner, etc.)
        if (!empty($photoSpecs['photo_static'])) {
            foreach ($photoSpecs['photo_static'] as $name => $spec) {
                $title = $spec['title'] ?? ucfirst($name);
                $w = $spec['width'] ?? 100;
                $h = $spec['height'] ?? 100;
                $thumb = "{$w}x{$h}x1";

                $content .= "\n/* $title */\n";
                $content .= "\$nametype = \"$name\";\n";
                $content .= "\$config['photo']['photo_static'][\$nametype]['title_main'] = \"$title\";\n";
                $content .= "\$config['photo']['photo_static'][\$nametype]['check'] = array(\"hienthi\" => hienthi);\n";
                $content .= "\$config['photo']['photo_static'][\$nametype]['images'] = true;\n";
                $content .= "\$config['photo']['photo_static'][\$nametype]['width'] = $w;\n";
                $content .= "\$config['photo']['photo_static'][\$nametype]['height'] = $h;\n";
                $content .= "\$config['photo']['photo_static'][\$nametype]['thumb'] = '$thumb';\n";
                $content .= "\$config['photo']['photo_static'][\$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';\n";
            }
        }

        // man_photo items (slideshow, social, partners, etc.)
        if (!empty($photoSpecs['man_photo'])) {
            foreach ($photoSpecs['man_photo'] as $name => $spec) {
                $title = $spec['title'] ?? ucfirst($name);
                $w = $spec['width'] ?? 100;
                $h = $spec['height'] ?? 100;
                $count = $spec['count'] ?? 5;
                $thumb = "{$w}x{$h}x1";

                $content .= "\n/* $title */\n";
                $content .= "\$nametype = \"$name\";\n";
                $content .= "\$config['photo']['man_photo'][\$nametype]['title_main_photo'] = \"$title\";\n";
                $content .= "\$config['photo']['man_photo'][\$nametype]['check_photo'] = array(\"hienthi\" => hienthi);\n";
                $content .= "\$config['photo']['man_photo'][\$nametype]['number_photo'] = $count;\n";
                $content .= "\$config['photo']['man_photo'][\$nametype]['images_photo'] = true;\n";
                $content .= "\$config['photo']['man_photo'][\$nametype]['avatar_photo'] = true;\n";

                // Add link for slideshow/social/partners
                if (in_array($name, ['slide', 'social', 'doitac'])) {
                    $content .= "\$config['photo']['man_photo'][\$nametype]['link_photo'] = true;\n";
                }

                $content .= "\$config['photo']['man_photo'][\$nametype]['name_photo'] = true;\n";

                // Add desc for slideshow
                if ($name === 'slide') {
                    $content .= "\$config['photo']['man_photo'][\$nametype]['desc_photo'] = true;\n";
                }

                $content .= "\$config['photo']['man_photo'][\$nametype]['width_photo'] = $w;\n";
                $content .= "\$config['photo']['man_photo'][\$nametype]['height_photo'] = $h;\n";
                $content .= "\$config['photo']['man_photo'][\$nametype]['thumb_photo'] = '$thumb';\n";
                $content .= "\$config['photo']['man_photo'][\$nametype]['img_type_photo'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';\n";
            }
        }

        if (strlen($content) > 10) {
            file_put_contents("$typeDir/config-type-photo.php", $content);
            $this->logger->info("Generated config for photo: config-type-photo.php");
        }
    }


    private function buildConfigBlock(string $base, string $nametype, array $spec): string
    {
        $width = $spec['width'] ?? 300;
        $height = $spec['height'] ?? 200;
        $thumb = "100x100x1";

        $features = $spec['features'] ?? [];
        $hasDetail = $spec['has_detail'] ?? false;

        // PHP code block
        $php = "\n/* " . ucfirst(str_replace('-', ' ', $nametype)) . " */\n";
        $php .= "\$nametype = \"$nametype\";\n";
        $php .= "\$config['$base'][\$nametype]['title_main'] = \"" . ucfirst(str_replace('-', ' ', $nametype)) . "\";\n";

        // Check options - always present
        $php .= "\$config['$base'][\$nametype]['check'] = array(\"hienthi\" => hienthi);\n";

        // Core flags from features
        if (!empty($features['view'])) {
            $php .= "\$config['$base'][\$nametype]['view'] = true;\n";
        }
        if (!empty($features['slug'])) {
            $php .= "\$config['$base'][\$nametype]['slug'] = true;\n";
        }
        if (!empty($features['copy'])) {
            $php .= "\$config['$base'][\$nametype]['copy'] = true;\n";
        }
        if (!empty($features['copy_image'])) {
            $php .= "\$config['$base'][\$nametype]['copy_image'] = true;\n";
        }

        // Images
        if (!empty($features['images'])) {
            $php .= "\$config['$base'][\$nametype]['images'] = true;\n";
            $php .= "\$config['$base'][\$nametype]['show_images'] = true;\n";
        }

        // Content
        if (!empty($features['desc'])) {
            $php .= "\$config['$base'][\$nametype]['desc'] = true;\n";
        }
        if (!empty($features['content'])) {
            $php .= "\$config['$base'][\$nametype]['content'] = true;\n";
        }
        if (!empty($features['content_cke'])) {
            $php .= "\$config['$base'][\$nametype]['content_cke'] = true;\n";
        }

        // SEO/Schema
        if (!empty($features['seo'])) {
            $php .= "\$config['$base'][\$nametype]['seo'] = true;\n";
        }
        if (!empty($features['schema'])) {
            $php .= "\$config['$base'][\$nametype]['schema'] = true;\n";
        }

        // Image dimensions
        $php .= "\$config['$base'][\$nametype]['width'] = $width;\n";
        $php .= "\$config['$base'][\$nametype]['height'] = $height;\n";
        $php .= "\$config['$base'][\$nametype]['thumb'] = '$thumb';\n";
        $php .= "\$config['$base'][\$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';\n";

        // Gallery (when detail page has multiple images)
        $hasGallery = $spec['has_gallery'] ?? false;
        if (($base === 'product' || $base === 'news') && $hasGallery) {
            $php .= "\$config['$base'][\$nametype]['gallery'] = array(\n";
            $php .= "    \$nametype => array(\n";
            $php .= "        \"title_main_photo\" => \"Hình ảnh\",\n";
            $php .= "        \"title_sub_photo\" => \"Hình ảnh\",\n";
            $php .= "        \"check_photo\" => array(\"hienthi\" => hienthi),\n";
            $php .= "        \"number_photo\" => 3,\n";
            $php .= "        \"images_photo\" => true,\n";
            $php .= "        \"avatar_photo\" => true,\n";
            $php .= "        \"name_photo\" => true,\n";
            $php .= "        \"width_photo\" => 540,\n";
            $php .= "        \"height_photo\" => 540,\n";
            $php .= "        \"thumb_photo\" => '100x100x1',\n";
            $php .= "        \"img_type_photo\" => '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP'\n";
            $php .= "    )\n";
            $php .= ");\n";
        }


        return $php . "\n";
    }
}
