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
