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
