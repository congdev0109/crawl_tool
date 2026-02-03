<?php

declare(strict_types=1);

namespace CrawlTool\Generator;

use CrawlTool\Utils\Logger;

/**
 * Generates PHP templates from crawled HTML
 * - Layout templates (header, footer, slide, etc.)
 * - Content templates (index, product, news, static, contact)
 */
class TemplateGenerator
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Generate templates from crawled HTML
     */
    public function generate(string $outputDir, array $specs, array $photoSpecs): void
    {
        $templatesDir = $outputDir . '/templates';
        if (!is_dir($templatesDir)) {
            mkdir($templatesDir, 0777, true);
        }

        // Create layout directory
        $layoutDir = "$templatesDir/layout";
        if (!is_dir($layoutDir)) {
            mkdir($layoutDir, 0777, true);
        }

        // Generate main layout files
        $this->generateMainIndex($templatesDir);
        $this->generateHead($layoutDir);
        $this->generateCss($layoutDir);
        $this->generateJs($layoutDir);
        $this->generateHeader($layoutDir, $photoSpecs);
        $this->generateMenu($layoutDir);
        $this->generateSlide($layoutDir, $photoSpecs);
        $this->generateFooter($layoutDir, $photoSpecs);
        $this->generateSeo($layoutDir);
        $this->generateBreadcrumb($layoutDir);

        // Generate content templates based on specs
        $this->generateContentTemplates($templatesDir, $specs);

        $this->logger->success("Generated templates/ files");
    }

    private function generateMainIndex(string $dir): void
    {
        $php = <<<'PHP'
<!DOCTYPE html>
<html lang="<?= $config['website']['lang-doc'] ?>">

<head>
    <?php include TEMPLATE . LAYOUT . "head.php"; ?>
    <?php include TEMPLATE . LAYOUT . "css.php"; ?>
</head>

<body>
    <?php
    include TEMPLATE . LAYOUT . "seo.php";
    include TEMPLATE . LAYOUT . "header.php";
    include TEMPLATE . LAYOUT . "menu.php";
    if ($source == 'index') include TEMPLATE . LAYOUT . "slide.php";
    else include TEMPLATE . LAYOUT . "breadcrumb.php";
    ?>
    <div class="<?= ($source == 'index') ? 'wrap-home' : 'wrap-content padding-top-bottom' ?>">
        <?php include TEMPLATE . $template . "_tpl.php"; ?>
    </div>
    <?php
    include TEMPLATE . LAYOUT . "footer.php";
    include TEMPLATE . LAYOUT . "js.php";
    ?>
</body>

</html>
PHP;
        file_put_contents("$dir/index.php", $php);
        $this->logger->info("Generated templates/index.php");
    }

    private function generateHead(string $dir): void
    {
        $php = <<<'PHP'
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="shortcut icon" href="<?= THUMBS ?>/48x48x1/<?= UPLOAD_PHOTO_L . ($favicon['photo'] ?? 'favicon.ico') ?>">
<title><?= $seo->get('title') ?></title>
<meta name="keywords" content="<?= $seo->get('keywords') ?>">
<meta name="description" content="<?= $seo->get('description') ?>">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?= $seo->get('url') ?>">
PHP;
        file_put_contents("$dir/head.php", $php);
        $this->logger->info("Generated templates/layout/head.php");
    }

    private function generateCss(string $dir): void
    {
        $php = <<<'PHP'
<?php $css->add('assets/css/bootstrap.min.css'); ?>
<?php $css->add('assets/css/owl.carousel.min.css'); ?>
<?php $css->add('assets/css/animate.min.css'); ?>
<?php $css->add('assets/css/style.css'); ?>
<?= $css->render() ?>
PHP;
        file_put_contents("$dir/css.php", $php);
        $this->logger->info("Generated templates/layout/css.php");
    }

    private function generateJs(string $dir): void
    {
        $php = <<<'PHP'
<?php $js->add('assets/js/jquery.min.js'); ?>
<?php $js->add('assets/js/bootstrap.bundle.min.js'); ?>
<?php $js->add('assets/js/owl.carousel.min.js'); ?>
<?php $js->add('assets/js/lazyload.min.js'); ?>
<?php $js->add('assets/js/main.js'); ?>
<?= $js->render() ?>
PHP;
        file_put_contents("$dir/js.php", $php);
        $this->logger->info("Generated templates/layout/js.php");
    }

    private function generateHeader(string $dir, array $photoSpecs): void
    {
        $logoWidth = $photoSpecs['photo_static']['logo']['width'] ?? 120;
        $logoHeight = $photoSpecs['photo_static']['logo']['height'] ?? 100;

        $php = <<<PHP
<header class="header">
    <div class="header-top">
        <div class="wrap-content d-flex flex-wrap justify-content-between align-items-center">
            <div class="header-contact d-flex align-items-center">
                <p class="info-header"><i class="bi bi-envelope"></i> Email: <?= \$optsetting['email'] ?></p>
                <p class="info-header ms-3"><i class="bi bi-telephone"></i> Hotline: <?= \$optsetting['hotline'] ?></p>
            </div>
            <?php if (!empty(\$social)) { ?>
            <div class="header-social d-flex align-items-center">
                <?php foreach (\$social as \$v) { ?>
                    <a href="<?= \$v['link'] ?>" target="_blank" title="<?= \$v['namevi'] ?>">
                        <img src="<?= THUMBS ?>/40x40x1/<?= UPLOAD_PHOTO_L . \$v['photo'] ?>" alt="<?= \$v['namevi'] ?>">
                    </a>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="header-main">
        <div class="wrap-content d-flex align-items-center justify-content-between">
            <a class="logo" href="">
                <img onerror="this.src='<?= THUMBS ?>/{$logoWidth}x{$logoHeight}x2/assets/images/noimage.png';" 
                     src="<?= THUMBS ?>/{$logoWidth}x{$logoHeight}x2/<?= UPLOAD_PHOTO_L . \$logo['photo'] ?>" 
                     alt="logo" title="logo" />
            </a>
            <div class="header-info">
                <p class="hotline">Hotline: <?= \$optsetting['hotline'] ?></p>
            </div>
        </div>
    </div>
</header>
PHP;
        file_put_contents("$dir/header.php", $php);
        $this->logger->info("Generated templates/layout/header.php");
    }

    private function generateMenu(string $dir): void
    {
        $php = <<<'PHP'
<nav class="main-menu">
    <div class="wrap-content">
        <div class="menu-toggle d-lg-none">
            <i class="bi bi-list"></i>
        </div>
        <ul class="menu-list d-flex flex-wrap align-items-center">
            <li><a href="" title="Trang chủ">Trang chủ</a></li>
            <?php
            $menuMain = $cache->get("select id, namevi, slugvi from #_menu where type = 'main' and find_in_set('hienthi',status) order by numb,id desc", null, 'query', 3600);
            foreach ($menuMain as $menu) {
            ?>
                <li><a href="<?= $menu['slugvi'] ?>" title="<?= $menu['namevi'] ?>"><?= $menu['namevi'] ?></a></li>
            <?php } ?>
        </ul>
    </div>
</nav>
PHP;
        file_put_contents("$dir/menu.php", $php);
        $this->logger->info("Generated templates/layout/menu.php");
    }

    private function generateSlide(string $dir, array $photoSpecs): void
    {
        $slideWidth = $photoSpecs['man_photo']['slide']['width'] ?? 1366;
        $slideHeight = $photoSpecs['man_photo']['slide']['height'] ?? 600;

        $php = <<<PHP
<?php if (!empty(\$slide)) { ?>
<div class="slideshow">
    <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:1" data-rewind="1" data-autoplay="1" data-loop="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplaytimeout="5000" data-dots="0" data-nav="1" data-navcontainer=".control-slideshow">
        <?php foreach (\$slide as \$v) { ?>
            <div class="slideshow-item">
                <a class="slideshow-image" href="<?= \$v['link'] ?>" title="<?= \$v['namevi'] ?>">
                    <picture>
                        <source media="(max-width: 500px)" srcset="<?= THUMBS ?>/500x200x1/<?= UPLOAD_PHOTO_L . \$v['photo'] ?>">
                        <img class="lazy w-100" 
                             onerror="this.src='<?= THUMBS ?>/{$slideWidth}x{$slideHeight}x1/assets/images/noimage.png';" 
                             data-src="<?= THUMBS ?>/{$slideWidth}x{$slideHeight}x1/<?= UPLOAD_PHOTO_L . \$v['photo'] ?>" 
                             alt="<?= \$v['namevi'] ?>" title="<?= \$v['namevi'] ?>" />
                    </picture>
                </a>
            </div>
        <?php } ?>
    </div>
    <div class="control-slideshow control-owl transition"></div>
</div>
<?php } ?>
PHP;
        file_put_contents("$dir/slide.php", $php);
        $this->logger->info("Generated templates/layout/slide.php");
    }

    private function generateFooter(string $dir, array $photoSpecs): void
    {
        $partnerWidth = $photoSpecs['man_photo']['doitac']['width'] ?? 175;
        $partnerHeight = $photoSpecs['man_photo']['doitac']['height'] ?? 95;

        $php = <<<PHP
<?php if (!empty(\$doitac)) { ?>
<div class="wrap-partner padding-top-bottom">
    <div class="wrap-content">
        <div class="title-main"><span>Đối tác</span></div>
        <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:575|items:3|margin:10,screen:991|items:5|margin:10" data-rewind="1" data-autoplay="1" data-loop="0" data-dots="0" data-nav="1" data-navcontainer=".control-partner">
            <?php foreach (\$doitac as \$v) { ?>
                <div>
                    <a class="partner" href="<?= \$v['link'] ?>" target="_blank" title="<?= \$v['namevi'] ?>">
                        <img class="w-100 lazy" 
                             onerror="this.src='<?= THUMBS ?>/{$partnerWidth}x{$partnerHeight}x2/assets/images/noimage.png';" 
                             data-src="<?= THUMBS ?>/{$partnerWidth}x{$partnerHeight}x2/<?= UPLOAD_PHOTO_L . \$v['photo'] ?>" 
                             alt="<?= \$v['namevi'] ?>" />
                    </a>
                </div>
            <?php } ?>
        </div>
        <div class="control-partner control-owl transition"></div>
    </div>
</div>
<?php } ?>

<footer class="footer">
    <div class="wrap-content">
        <div class="row">
            <div class="col-lg-4">
                <h4 class="footer-title">Thông tin liên hệ</h4>
                <p><i class="bi bi-geo-alt"></i> <?= \$optsetting['diachi'] ?></p>
                <p><i class="bi bi-telephone"></i> <?= \$optsetting['hotline'] ?></p>
                <p><i class="bi bi-envelope"></i> <?= \$optsetting['email'] ?></p>
            </div>
            <div class="col-lg-4">
                <h4 class="footer-title">Liên kết</h4>
                <?php
                \$menuFooter = \$cache->get("select id, namevi, slugvi from #_menu where type = 'footer' and find_in_set('hienthi',status) order by numb,id desc", null, 'query', 3600);
                foreach (\$menuFooter as \$menu) {
                ?>
                    <p><a href="<?= \$menu['slugvi'] ?>"><?= \$menu['namevi'] ?></a></p>
                <?php } ?>
            </div>
            <div class="col-lg-4">
                <h4 class="footer-title">Fanpage</h4>
                <div class="footer-fanpage">
                    <!-- Facebook Page Plugin -->
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <p>&copy; <?= date('Y') ?> <?= \$optsetting['ten-cong-ty'] ?>. All rights reserved.</p>
        </div>
    </div>
</footer>
PHP;
        file_put_contents("$dir/footer.php", $php);
        $this->logger->info("Generated templates/layout/footer.php");
    }

    private function generateSeo(string $dir): void
    {
        $php = <<<'PHP'
<!-- Open Graph / Facebook -->
<meta property="og:type" content="<?= $seo->get('type') ?>">
<meta property="og:url" content="<?= $seo->get('url') ?>">
<meta property="og:title" content="<?= $seo->get('title') ?>">
<meta property="og:description" content="<?= $seo->get('description') ?>">
<?php if ($seo->get('photo')) { ?>
<meta property="og:image" content="<?= $seo->get('photo') ?>">
<?php } ?>

<!-- JSON-LD Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "<?= $optsetting['ten-cong-ty'] ?>",
    "url": "<?= $configBase ?>",
    "logo": "<?= $configBase ?><?= THUMBS ?>/120x100x1/<?= UPLOAD_PHOTO_L . $logo['photo'] ?>",
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?= $optsetting['hotline'] ?>",
        "contactType": "Customer Service"
    }
}
</script>
PHP;
        file_put_contents("$dir/seo.php", $php);
        $this->logger->info("Generated templates/layout/seo.php");
    }

    private function generateBreadcrumb(string $dir): void
    {
        $php = <<<'PHP'
<?php if (!empty($breadcrumbs) && count($breadcrumbs) > 0) { ?>
<div class="breadcrumb-wrap">
    <div class="wrap-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Trang chủ</a></li>
                <?php foreach ($breadcrumbs as $slug => $name) { ?>
                    <li class="breadcrumb-item"><a href="<?= $slug ?>"><?= $name ?></a></li>
                <?php } ?>
            </ol>
        </nav>
    </div>
</div>
<?php } ?>
PHP;
        file_put_contents("$dir/breadcrumb.php", $php);
        $this->logger->info("Generated templates/layout/breadcrumb.php");
    }

    private function generateContentTemplates(string $templatesDir, array $specs): void
    {
        // Create index template
        $indexDir = "$templatesDir/index";
        if (!is_dir($indexDir)) mkdir($indexDir, 0777, true);
        $this->generateIndexTpl($indexDir);

        // Create templates based on base types
        $bases = [];
        foreach ($specs as $nametype => $spec) {
            $base = $spec['base'] ?? 'other';
            if ($base !== 'other' && !in_array($base, $bases)) {
                $bases[] = $base;
            }
        }

        foreach ($bases as $base) {
            $baseDir = "$templatesDir/$base";
            if (!is_dir($baseDir)) mkdir($baseDir, 0777, true);

            match ($base) {
                'product' => $this->generateProductTemplates($baseDir),
                'news' => $this->generateNewsTemplates($baseDir),
                'static' => $this->generateStaticTemplate($baseDir),
                default => null
            };
        }

        // Create contact template
        $contactDir = "$templatesDir/contact";
        if (!is_dir($contactDir)) mkdir($contactDir, 0777, true);
        $this->generateContactTemplate($contactDir);
    }

    private function generateIndexTpl(string $dir): void
    {
        $php = <<<'PHP'
<!-- Featured Products -->
<?php if (!empty($featuredProducts)) { ?>
<div class="section-product padding-top-bottom">
    <div class="wrap-content">
        <div class="title-main"><span>Sản phẩm nổi bật</span></div>
        <div class="row">
            <?php foreach ($featuredProducts as $v) { ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="box-product">
                        <a class="pic-product scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['namevi'] ?>">
                            <img class="lazy w-100" 
                                 onerror="this.src='<?= THUMBS ?>/285x285x1/assets/images/noimage.png';" 
                                 data-src="<?= THUMBS ?>/285x285x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" 
                                 alt="<?= $v['namevi'] ?>" />
                        </a>
                        <h3 class="name-product"><a href="<?= $v['slugvi'] ?>"><?= $v['namevi'] ?></a></h3>
                        <p class="price-product">
                            <?php if ($v['sale_price'] && $v['sale_price'] < $v['regular_price']) { ?>
                                <span class="price-new"><?= $func->formatMoney($v['sale_price']) ?></span>
                                <span class="price-old"><?= $func->formatMoney($v['regular_price']) ?></span>
                            <?php } else { ?>
                                <span class="price-new"><?= ($v['regular_price']) ? $func->formatMoney($v['regular_price']) : lienhe ?></span>
                            <?php } ?>
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<!-- Latest News -->
<?php if (!empty($latestNews)) { ?>
<div class="section-news padding-top-bottom">
    <div class="wrap-content">
        <div class="title-main"><span>Tin tức mới nhất</span></div>
        <div class="row">
            <?php foreach ($latestNews as $v) { ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="box-news">
                        <a class="pic-news scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['namevi'] ?>">
                            <img class="lazy w-100" 
                                 onerror="this.src='<?= THUMBS ?>/420x288x1/assets/images/noimage.png';" 
                                 data-src="<?= THUMBS ?>/420x288x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" 
                                 alt="<?= $v['namevi'] ?>" />
                        </a>
                        <h3 class="name-news"><a href="<?= $v['slugvi'] ?>"><?= $v['namevi'] ?></a></h3>
                        <p class="date-news"><?= date("d/m/Y", $v['date_created']) ?></p>
                        <p class="desc-news text-split"><?= $v['descvi'] ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
PHP;
        file_put_contents("$dir/index_tpl.php", $php);
        $this->logger->info("Generated templates/index/index_tpl.php");
    }

    private function generateProductTemplates(string $dir): void
    {
        // Product list template
        $listPhp = <<<'PHP'
<div class="section-product">
    <div class="wrap-content">
        <h1 class="title-page"><?= $seo->get('h1') ?></h1>
        
        <?php if (!empty($product)) { ?>
        <div class="row">
            <?php foreach ($product as $v) { ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="box-product">
                        <a class="pic-product scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['name' . $lang] ?>">
                            <img class="lazy w-100" 
                                 onerror="this.src='<?= THUMBS ?>/285x285x1/assets/images/noimage.png';" 
                                 data-src="<?= THUMBS ?>/285x285x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" 
                                 alt="<?= $v['name' . $lang] ?>" />
                        </a>
                        <h3 class="name-product"><a href="<?= $v['slugvi'] ?>"><?= $v['name' . $lang] ?></a></h3>
                        <p class="price-product">
                            <?php if ($v['sale_price'] && $v['sale_price'] < $v['regular_price']) { ?>
                                <span class="price-new"><?= $func->formatMoney($v['sale_price']) ?></span>
                                <span class="price-old"><?= $func->formatMoney($v['regular_price']) ?></span>
                            <?php } else { ?>
                                <span class="price-new"><?= ($v['regular_price']) ? $func->formatMoney($v['regular_price']) : lienhe ?></span>
                            <?php } ?>
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
        
        <?php if (!empty($paging)) { ?>
            <div class="paging"><?= $paging ?></div>
        <?php } ?>
        <?php } else { ?>
            <p class="no-data">Không có dữ liệu</p>
        <?php } ?>
    </div>
</div>
PHP;
        file_put_contents("$dir/product_tpl.php", $listPhp);
        $this->logger->info("Generated templates/product/product_tpl.php");

        // Product detail template
        $detailPhp = <<<'PHP'
<div class="product-detail">
    <div class="wrap-content">
        <div class="row">
            <div class="col-lg-5">
                <div class="product-gallery">
                    <div class="product-main-image">
                        <img id="mainImage" src="<?= THUMBS ?>/500x500x2/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" 
                             alt="<?= $rowDetail['name' . $lang] ?>" />
                    </div>
                    <?php if (!empty($rowDetailPhoto)) { ?>
                    <div class="product-thumbs">
                        <?php foreach ($rowDetailPhoto as $img) { ?>
                            <img class="thumb-item" 
                                 src="<?= THUMBS ?>/100x100x1/<?= UPLOAD_GALLERY_L . $img['photo'] ?>" 
                                 data-full="<?= THUMBS ?>/500x500x2/<?= UPLOAD_GALLERY_L . $img['photo'] ?>"
                                 alt="<?= $rowDetail['name' . $lang] ?>" />
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="product-info">
                    <h1 class="product-title"><?= $rowDetail['name' . $lang] ?></h1>
                    <div class="product-price">
                        <?php if ($rowDetail['sale_price'] && $rowDetail['sale_price'] < $rowDetail['regular_price']) { ?>
                            <span class="price-new"><?= $func->formatMoney($rowDetail['sale_price']) ?></span>
                            <span class="price-old"><?= $func->formatMoney($rowDetail['regular_price']) ?></span>
                        <?php } else { ?>
                            <span class="price-new"><?= ($rowDetail['regular_price']) ? $func->formatMoney($rowDetail['regular_price']) : lienhe ?></span>
                        <?php } ?>
                    </div>
                    <div class="product-desc">
                        <?= $rowDetail['desc' . $lang] ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="product-content mt-4">
            <h2>Mô tả chi tiết</h2>
            <div class="content-body">
                <?= $rowDetail['content' . $lang] ?>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($product)) { ?>
        <div class="related-products mt-4">
            <h2>Sản phẩm liên quan</h2>
            <div class="row">
                <?php foreach ($product as $v) { ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <div class="box-product">
                            <a class="pic-product scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['name' . $lang] ?>">
                                <img class="lazy w-100" 
                                     onerror="this.src='<?= THUMBS ?>/285x285x1/assets/images/noimage.png';" 
                                     data-src="<?= THUMBS ?>/285x285x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" 
                                     alt="<?= $v['name' . $lang] ?>" />
                            </a>
                            <h3 class="name-product"><a href="<?= $v['slugvi'] ?>"><?= $v['name' . $lang] ?></a></h3>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
PHP;
        file_put_contents("$dir/product_detail_tpl.php", $detailPhp);
        $this->logger->info("Generated templates/product/product_detail_tpl.php");
    }

    private function generateNewsTemplates(string $dir): void
    {
        // News list template
        $listPhp = <<<'PHP'
<div class="section-news">
    <div class="wrap-content">
        <h1 class="title-page"><?= $seo->get('h1') ?></h1>
        
        <?php if (!empty($news)) { ?>
        <div class="row">
            <?php foreach ($news as $v) { ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="box-news">
                        <a class="pic-news scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['name' . $lang] ?>">
                            <img class="lazy w-100" 
                                 onerror="this.src='<?= THUMBS ?>/420x288x1/assets/images/noimage.png';" 
                                 data-src="<?= THUMBS ?>/420x288x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" 
                                 alt="<?= $v['name' . $lang] ?>" />
                        </a>
                        <div class="info-news">
                            <h3 class="name-news"><a href="<?= $v['slugvi'] ?>"><?= $v['name' . $lang] ?></a></h3>
                            <p class="date-news"><?= date("d/m/Y", $v['date_created']) ?></p>
                            <p class="desc-news text-split"><?= $v['desc' . $lang] ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        
        <?php if (!empty($paging)) { ?>
            <div class="paging"><?= $paging ?></div>
        <?php } ?>
        <?php } else { ?>
            <p class="no-data">Không có dữ liệu</p>
        <?php } ?>
    </div>
</div>
PHP;
        file_put_contents("$dir/news_tpl.php", $listPhp);
        $this->logger->info("Generated templates/news/news_tpl.php");

        // News detail template
        $detailPhp = <<<'PHP'
<div class="news-detail">
    <div class="wrap-content">
        <div class="row">
            <div class="col-lg-8">
                <article class="news-article">
                    <h1 class="news-title"><?= $rowDetail['name' . $lang] ?></h1>
                    <p class="news-date"><i class="bi bi-calendar"></i> <?= date("d/m/Y", $rowDetail['date_created']) ?></p>
                    <div class="news-desc">
                        <?= $rowDetail['desc' . $lang] ?>
                    </div>
                    <div class="news-content">
                        <?= $rowDetail['content' . $lang] ?>
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <div class="sidebar">
                    <h3>Bài viết liên quan</h3>
                    <?php if (!empty($news)) { ?>
                        <?php foreach ($news as $v) { ?>
                            <div class="sidebar-item d-flex mb-3">
                                <a class="sidebar-img" href="<?= $v['slugvi'] ?>">
                                    <img src="<?= THUMBS ?>/100x68x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>">
                                </a>
                                <div class="sidebar-info">
                                    <a href="<?= $v['slugvi'] ?>"><?= $v['name' . $lang] ?></a>
                                    <p><?= date("d/m/Y", $v['date_created']) ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
PHP;
        file_put_contents("$dir/news_detail_tpl.php", $detailPhp);
        $this->logger->info("Generated templates/news/news_detail_tpl.php");
    }

    private function generateStaticTemplate(string $dir): void
    {
        $php = <<<'PHP'
<div class="static-page">
    <div class="wrap-content">
        <h1 class="title-page"><?= $rowDetail['name' . $lang] ?></h1>
        <div class="static-content">
            <?= $rowDetail['content' . $lang] ?>
        </div>
    </div>
</div>
PHP;
        file_put_contents("$dir/static_tpl.php", $php);
        $this->logger->info("Generated templates/static/static_tpl.php");
    }

    private function generateContactTemplate(string $dir): void
    {
        $php = <<<'PHP'
<div class="contact-page">
    <div class="wrap-content">
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-info">
                    <h1 class="contact-title">Liên hệ với chúng tôi</h1>
                    <p><i class="bi bi-building"></i> <strong><?= $optsetting['ten-cong-ty'] ?></strong></p>
                    <p><i class="bi bi-geo-alt"></i> <?= $optsetting['diachi'] ?></p>
                    <p><i class="bi bi-telephone"></i> <?= $optsetting['hotline'] ?></p>
                    <p><i class="bi bi-envelope"></i> <?= $optsetting['email'] ?></p>
                </div>
                <div class="contact-map mt-4">
                    <?php if (!empty($optsetting['map'])) { ?>
                        <?= $optsetting['map'] ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-form">
                    <h2>Gửi tin nhắn</h2>
                    <?php if ($flash->has('success')) { ?>
                        <div class="alert alert-success"><?= $flash->get('success') ?></div>
                    <?php } ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Họ tên *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội dung</label>
                            <textarea name="content" class="form-control" rows="5"></textarea>
                        </div>
                        <button type="submit" name="contact_submit" class="btn btn-primary">Gửi liên hệ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
PHP;
        file_put_contents("$dir/contact_tpl.php", $php);
        $this->logger->info("Generated templates/contact/contact_tpl.php");
    }
}
