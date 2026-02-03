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
