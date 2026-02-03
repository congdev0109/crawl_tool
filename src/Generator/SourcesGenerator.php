<?php

declare(strict_types=1);

namespace CrawlTool\Generator;

use CrawlTool\Utils\Logger;

/**
 * Generates sources/*.php files for data queries
 * Each source file handles list/detail views for a specific content type
 */
class SourcesGenerator
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Generate sources directory with PHP files
     */
    public function generate(string $outputDir, array $specs): void
    {
        $sourcesDir = $outputDir . '/sources';
        if (!is_dir($sourcesDir)) {
            mkdir($sourcesDir, 0777, true);
        }

        // Generate allpage.php (common data for all pages)
        $this->generateAllpage($sourcesDir);

        // Generate index.php (homepage)
        $this->generateIndex($sourcesDir);

        // Generate source files based on base types
        $bases = [];
        foreach ($specs as $nametype => $spec) {
            $base = $spec['base'] ?? 'other';
            if ($base !== 'other' && !in_array($base, $bases)) {
                $bases[] = $base;
            }
        }

        foreach ($bases as $base) {
            match ($base) {
                'product' => $this->generateProduct($sourcesDir),
                'news' => $this->generateNews($sourcesDir),
                'static' => $this->generateStatic($sourcesDir),
                default => null
            };
        }

        // Generate contact.php
        $this->generateContact($sourcesDir);

        $this->logger->success("Generated sources/ files");
    }

    private function generateAllpage(string $dir): void
    {
        $php = <<<'PHP'
<?php
if (!defined('SOURCES')) die("Error");

/* Logo */
$logo = $cache->get("select photo from #_photo where type = 'logo' and act = 'photo_static' and find_in_set('hienthi',status) order by id desc limit 0,1", null, 'fetch', 3600);

/* Favicon */
$favicon = $cache->get("select photo from #_photo where type = 'favicon' and act = 'photo_static' and find_in_set('hienthi',status) order by id desc limit 0,1", null, 'fetch', 3600);

/* Social */
$social = $cache->get("select id, photo, link, namevi from #_photo where type = 'social' and act = 'man_photo' and find_in_set('hienthi',status) order by numb,id desc", null, 'query', 3600);

/* Slideshow */
$slide = $cache->get("select id, photo, link, namevi, descvi from #_photo where type = 'slide' and act = 'man_photo' and find_in_set('hienthi',status) order by numb,id desc", null, 'query', 3600);

/* Partners */
$doitac = $cache->get("select id, photo, link, namevi from #_photo where type = 'doitac' and act = 'man_photo' and find_in_set('hienthi',status) order by numb,id desc", null, 'query', 3600);

PHP;
        file_put_contents("$dir/allpage.php", $php);
        $this->logger->info("Generated sources/allpage.php");
    }

    private function generateIndex(string $dir): void
    {
        $php = <<<'PHP'
<?php
if (!defined('SOURCES')) die("Error");

/* SEO */
$seopage = $d->rawQueryOne("select * from #_seopage where type = 'trang-chu' limit 0,1");
$seo->set('h1', !empty($optsetting['ten-cong-ty']) ? $optsetting['ten-cong-ty'] : '');
if (!empty($seopage['titlevi'])) $seo->set('title', $seopage['titlevi']);
if (!empty($seopage['keywordsvi'])) $seo->set('keywords', $seopage['keywordsvi']);
if (!empty($seopage['descriptionvi'])) $seo->set('description', $seopage['descriptionvi']);
$seo->set('url', $func->getPageURL());

/* Featured Products - Example query */
$featuredProducts = $d->rawQuery("select id, namevi, slugvi, photo, sale_price, regular_price from #_product where find_in_set('noibat',status) and find_in_set('hienthi',status) order by numb,id desc limit 0,8");

/* Latest News - Example query */
$latestNews = $d->rawQuery("select id, namevi, slugvi, photo, descvi, date_created from #_news where find_in_set('hienthi',status) order by numb,id desc limit 0,4");

PHP;
        file_put_contents("$dir/index.php", $php);
        $this->logger->info("Generated sources/index.php");
    }

    private function generateProduct(string $dir): void
    {
        $php = <<<'PHP'
<?php
if (!defined('SOURCES')) die("Error");

@$id = htmlspecialchars($_GET['id']);
@$idl = htmlspecialchars($_GET['idl']);
@$idc = htmlspecialchars($_GET['idc']);
@$idi = htmlspecialchars($_GET['idi']);
@$ids = htmlspecialchars($_GET['ids']);

if ($id != '') {
    /* Get product detail */
    $rowDetail = $d->rawQueryOne("select type, id, name$lang, slugvi, desc$lang, content$lang, code, view, id_list, id_cat, id_item, id_sub, photo, options from #_product where id = ? and type = ? and find_in_set('hienthi',status) limit 0,1", array($id, $type));

    /* Update view count */
    $views = array();
    $views['view'] = $rowDetail['view'] + 1;
    $d->where('id', $rowDetail['id']);
    $d->update('product', $views);

    /* Get list level */
    $productList = $d->rawQueryOne("select id, name$lang, slugvi from #_product_list where id = ? and type = ? and find_in_set('hienthi',status) limit 0,1", array($rowDetail['id_list'], $type));

    /* Get gallery images */
    $rowDetailPhoto = $d->rawQuery("select photo from #_gallery where id_parent = ? and com='product' and type = ? and kind='man' and val = ? and find_in_set('hienthi',status) order by numb,id desc", array($rowDetail['id'], $type, $type));

    /* Related products */
    $curPage = $getPage;
    $perPage = 8;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select photo, name$lang, slugvi, sale_price, regular_price, id from #_product where id <> ? and id_list = ? and type = ? and find_in_set('hienthi',status) order by numb,id desc $limit";
    $product = $d->rawQuery($sql, array($id, $rowDetail['id_list'], $type));

    /* SEO */
    $seo->set('h1', $rowDetail['name' . $lang]);
    $seo->set('title', $rowDetail['name' . $lang]);
    $seo->set('url', $func->getPageURL());

    /* breadCrumbs */
    if (!empty($titleMain)) $breadcr->set($com, $titleMain);
    if (!empty($productList)) $breadcr->set($productList['slugvi'], $productList['name' . $lang]);
    $breadcr->set($rowDetail['slugvi'], $rowDetail['name' . $lang]);
    $breadcrumbs = $breadcr->get();

} else if ($idl != '') {
    /* Get list detail */
    $productList = $d->rawQueryOne("select id, name$lang, slugvi, type, photo from #_product_list where id = ? and type = ? limit 0,1", array($idl, $type));

    /* Get products in list */
    $curPage = $getPage;
    $perPage = 20;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select photo, name$lang, slugvi, sale_price, regular_price, id from #_product where id_list = ? and type = ? and find_in_set('hienthi',status) order by numb,id desc $limit";
    $product = $d->rawQuery($sql, array($idl, $type));
    $sqlNum = "select count(*) as 'num' from #_product where id_list = ? and type = ? and find_in_set('hienthi',status)";
    $count = $d->rawQueryOne($sqlNum, array($idl, $type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = $func->getCurrentPageURL();
    $paging = $func->pagination($total, $perPage, $curPage, $url);

    /* SEO */
    $seo->set('h1', $productList['name' . $lang]);
    $seo->set('title', $productList['name' . $lang]);

    /* breadCrumbs */
    if (!empty($titleMain)) $breadcr->set($com, $titleMain);
    if (!empty($productList)) $breadcr->set($productList['slugvi'], $productList['name' . $lang]);
    $breadcrumbs = $breadcr->get();

} else {
    /* Get all products */
    $curPage = $getPage;
    $perPage = 20;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select photo, name$lang, slugvi, sale_price, regular_price, id from #_product where type = ? and find_in_set('hienthi',status) order by numb,id desc $limit";
    $product = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_product where type = ? and find_in_set('hienthi',status)";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = $func->getCurrentPageURL();
    $paging = $func->pagination($total, $perPage, $curPage, $url);

    /* SEO */
    $seo->set('h1', $titleMain);
    $seo->set('title', $titleMain);

    /* breadCrumbs */
    if (!empty($titleMain)) $breadcr->set($com, $titleMain);
    $breadcrumbs = $breadcr->get();
}

PHP;
        file_put_contents("$dir/product.php", $php);
        $this->logger->info("Generated sources/product.php");
    }

    private function generateNews(string $dir): void
    {
        $php = <<<'PHP'
<?php
if (!defined('SOURCES')) die("Error");

@$id = htmlspecialchars($_GET['id']);
@$idl = htmlspecialchars($_GET['idl']);
@$idc = htmlspecialchars($_GET['idc']);
@$idi = htmlspecialchars($_GET['idi']);
@$ids = htmlspecialchars($_GET['ids']);

if ($id != '') {
    /* Get news detail */
    $rowDetail = $d->rawQueryOne("select id, view, date_created, id_list, id_cat, id_item, id_sub, type, name$lang, slugvi, desc$lang, content$lang, photo, options from #_news where id = ? and type = ? and find_in_set('hienthi',status) limit 0,1", array($id, $type));

    /* Update view count */
    $views = array();
    $views['view'] = $rowDetail['view'] + 1;
    $d->where('id', $rowDetail['id']);
    $d->update('news', $views);

    /* Get list level */
    $newsList = $d->rawQueryOne("select id, name$lang, slugvi from #_news_list where id = ? and type = ? and find_in_set('hienthi',status) limit 0,1", array($rowDetail['id_list'], $type));

    /* Related news */
    $curPage = $getPage;
    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select id, name$lang, slugvi, photo, date_created, desc$lang from #_news where id <> ? and id_list = ? and type = ? and find_in_set('hienthi',status) order by numb,id desc $limit";
    $news = $d->rawQuery($sql, array($id, $rowDetail['id_list'], $type));

    /* SEO */
    $seo->set('h1', $rowDetail['name' . $lang]);
    $seo->set('title', $rowDetail['name' . $lang]);
    $seo->set('url', $func->getPageURL());

    /* breadCrumbs */
    if (!empty($titleMain)) $breadcr->set($com, $titleMain);
    if (!empty($newsList)) $breadcr->set($newsList['slugvi'], $newsList['name' . $lang]);
    $breadcr->set($rowDetail['slugvi'], $rowDetail['name' . $lang]);
    $breadcrumbs = $breadcr->get();

} else if ($idl != '') {
    /* Get list detail */
    $newsList = $d->rawQueryOne("select id, name$lang, slugvi, type, photo from #_news_list where id = ? and type = ? limit 0,1", array($idl, $type));

    /* Get news in list */
    $curPage = $getPage;
    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select id, name$lang, slugvi, photo, date_created, desc$lang from #_news where id_list = ? and type = ? and find_in_set('hienthi',status) order by numb,id desc $limit";
    $news = $d->rawQuery($sql, array($idl, $type));
    $sqlNum = "select count(*) as 'num' from #_news where id_list = ? and type = ? and find_in_set('hienthi',status)";
    $count = $d->rawQueryOne($sqlNum, array($idl, $type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = $func->getCurrentPageURL();
    $paging = $func->pagination($total, $perPage, $curPage, $url);

    /* SEO */
    $seo->set('h1', $newsList['name' . $lang]);
    $seo->set('title', $newsList['name' . $lang]);

    /* breadCrumbs */
    if (!empty($titleMain)) $breadcr->set($com, $titleMain);
    if (!empty($newsList)) $breadcr->set($newsList['slugvi'], $newsList['name' . $lang]);
    $breadcrumbs = $breadcr->get();

} else {
    /* Get all news */
    $curPage = $getPage;
    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select id, name$lang, slugvi, photo, date_created, desc$lang from #_news where type = ? and find_in_set('hienthi',status) order by numb,id desc $limit";
    $news = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_news where type = ? and find_in_set('hienthi',status)";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = $func->getCurrentPageURL();
    $paging = $func->pagination($total, $perPage, $curPage, $url);

    /* SEO */
    $seo->set('h1', $titleMain);
    $seo->set('title', $titleMain);

    /* breadCrumbs */
    if (!empty($titleMain)) $breadcr->set($com, $titleMain);
    $breadcrumbs = $breadcr->get();
}

PHP;
        file_put_contents("$dir/news.php", $php);
        $this->logger->info("Generated sources/news.php");
    }

    private function generateStatic(string $dir): void
    {
        $php = <<<'PHP'
<?php
if (!defined('SOURCES')) die("Error");

/* Get static page content */
$rowDetail = $d->rawQueryOne("select id, name$lang, slugvi, desc$lang, content$lang, photo, options from #_static where type = ? and find_in_set('hienthi',status) limit 0,1", array($type));

/* SEO */
$seoDB = $seo->getOnDB($rowDetail['id'], 'static', 'man', $type);
$seo->set('h1', $rowDetail['name' . $lang]);
if (!empty($seoDB['titlevi'])) $seo->set('title', $seoDB['titlevi']);
else $seo->set('title', $rowDetail['name' . $lang]);
if (!empty($seoDB['keywordsvi'])) $seo->set('keywords', $seoDB['keywordsvi']);
if (!empty($seoDB['descriptionvi'])) $seo->set('description', $seoDB['descriptionvi']);
$seo->set('url', $func->getPageURL());

/* breadCrumbs */
if (!empty($titleMain)) $breadcr->set($com, $titleMain);
$breadcrumbs = $breadcr->get();

PHP;
        file_put_contents("$dir/static.php", $php);
        $this->logger->info("Generated sources/static.php");
    }

    private function generateContact(string $dir): void
    {
        $php = <<<'PHP'
<?php
if (!defined('SOURCES')) die("Error");

/* SEO */
$seo->set('h1', lienhe);
$seo->set('title', lienhe);
$seo->set('url', $func->getPageURL());

/* breadCrumbs */
$breadcr->set('lien-he', lienhe);
$breadcrumbs = $breadcr->get();

/* Handle contact form submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contact_submit'])) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $content = htmlspecialchars($_POST['content'] ?? '');

    if (!empty($name) && !empty($email)) {
        $data = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'content' => $content,
            'status' => 'chuaxuly',
            'date_created' => date('Y-m-d H:i:s')
        );
        $d->insert('contact', $data);
        $flash->set('success', 'Gửi liên hệ thành công!');
    }
}

PHP;
        file_put_contents("$dir/contact.php", $php);
        $this->logger->info("Generated sources/contact.php");
    }
}
