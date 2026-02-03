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
