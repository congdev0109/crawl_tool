<?php
if (!defined('SOURCES')) die("Error");
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);
/* Kiểm tra active product */
if (isset($config['product'])) {
    $arrCheck = array();
    foreach ($config['product'] as $k => $v) $arrCheck[] = $k;
    if (!count($arrCheck) || !in_array($type, $arrCheck)) $func->transfer(trangkhongtontai, "index.php", false);
} else {
    $func->transfer(trangkhongtontai, "index.php", false);
}

/* Cấu hình đường dẫn trả về */
$strUrl = "";
$arrUrl = array('id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand');
if (isset($_POST['data'])) {
    $dataUrl = isset($_POST['data']) ? $_POST['data'] : null;
    if ($dataUrl) {
        foreach ($arrUrl as $k => $v) {
            if (isset($dataUrl[$arrUrl[$k]])) $strUrl .= "&" . $arrUrl[$k] . "=" . htmlspecialchars($dataUrl[$arrUrl[$k]]);
        }
    }
} else {
    foreach ($arrUrl as $k => $v) {
        if (isset($_REQUEST[$arrUrl[$k]])) $strUrl .= "&" . $arrUrl[$k] . "=" . htmlspecialchars($_REQUEST[$arrUrl[$k]]);
    }

    if (!empty($_REQUEST['comment_status'])) $strUrl .= "&comment_status=" . htmlspecialchars($_REQUEST['comment_status']);
    if (isset($_REQUEST['keyword'])) $strUrl .= "&keyword=" . htmlspecialchars($_REQUEST['keyword']);
}

switch ($act) {
        /* Man */
    case "man":
        viewMans();
        $template = "product/man/mans";
        break;
    case "add":
        $template = "product/man/man_add";
        break;
    case "edit":
    case "copy":
        if ((!isset($config['product'][$type]['copy']) || $config['product'][$type]['copy'] == false) && $act == 'copy') {
            $template = "404";
            return false;
        }
        editMan();
        $template = "product/man/man_add";
        break;
    case "save":
    case "save_copy":
        saveMan();
        break;
    case "delete":
        deleteMan();
        break;

        /* Size */
    case "man_size":
        viewSizes();
        $template = "product/size/sizes";
        break;
    case "add_size":
        $template = "product/size/size_add";
        break;
    case "edit_size":
        editSize();
        $template = "product/size/size_add";
        break;
    case "save_size":
        saveSize();
        break;
    case "delete_size":
        deleteSize();
        break;

        /* dataSpecifications */
    case "man_specifications":
        viewSpecifications();
        $template = "product/specifications/specifications";
        break;
    case "add_specifications":
        $template = "product/specifications/specifications_add";
        break;
    case "edit_specifications":
        editSpecifications();
        $template = "product/specifications/specifications_add";
        break;
    case "save_specifications":
        saveSpecifications();
        break;
    case "delete_specifications":
        deleteSpecifications();
        break;



        /* Color */
    case "man_color":
        viewColors();
        $template = "product/color/colors";
        break;
    case "add_color":
        $template = "product/color/color_add";
        break;
    case "edit_color":
        editColor();
        $template = "product/color/color_add";
        break;
    case "save_color":
        saveColor();
        break;
    case "delete_color":
        deleteColor();
        break;

        /* Brand */
    case "man_brand":
        viewBrands();
        $template = "product/brand/brand";
        break;
    case "add_brand":
        $template = "product/brand/brand_add";
        break;
    case "edit_brand":
        editBrand();
        $template = "product/brand/brand_add";
        break;
    case "save_brand":
        saveBrand();
        break;
    case "delete_brand":
        deleteBrand();
        break;

        /* List */
    case "man_list":
        viewLists();
        $template = "product/list/lists";
        break;
    case "add_list":
        $template = "product/list/list_add";
        break;
    case "edit_list":
        editList();
        $template = "product/list/list_add";
        break;
    case "save_list":
        saveList();
        break;
    case "delete_list":
        deleteList();
        break;

        /* Cat */
    case "man_cat":
        viewCats();
        $template = "product/cat/cats";
        break;
    case "add_cat":
        $template = "product/cat/cat_add";
        break;
    case "edit_cat":
        editCat();
        $template = "product/cat/cat_add";
        break;
    case "save_cat":
        saveCat();
        break;
    case "delete_cat":
        deleteCat();
        break;

        /* Item */
    case "man_item":
        viewItems();
        $template = "product/item/items";
        break;
    case "add_item":
        $template = "product/item/item_add";
        break;
    case "edit_item":
        editItem();
        $template = "product/item/item_add";
        break;
    case "save_item":
        saveItem();
        break;
    case "delete_item":
        deleteItem();
        break;

        /* Sub */
    case "man_sub":
        viewSubs();
        $template = "product/sub/subs";
        break;
    case "add_sub":
        $template = "product/sub/sub_add";
        break;
    case "edit_sub":
        editSub();
        $template = "product/sub/sub_add";
        break;
    case "save_sub":
        saveSub();
        break;
    case "delete_sub":
        deleteSub();
        break;

        /* Gallery */
    case "man_photo":
    case "add_photo":
    case "edit_photo":
    case "save_photo":
    case "delete_photo":
        include "gallery.php";
        break;

    default:
        $template = "404";
}

/* View man */
function viewMans()
{
    global $d, $langadmin, $func, $comment, $strUrl, $curPage, $items, $paging, $type;

    $where = "";
    $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
    $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
    $iditem = (isset($_REQUEST['id_item'])) ? htmlspecialchars($_REQUEST['id_item']) : 0;
    $idsub = (isset($_REQUEST['id_sub'])) ? htmlspecialchars($_REQUEST['id_sub']) : 0;
    $idbrand = (isset($_REQUEST['id_brand'])) ? htmlspecialchars($_REQUEST['id_brand']) : 0;
    $comment_status = (!empty($_REQUEST['comment_status'])) ? htmlspecialchars($_REQUEST['comment_status']) : '';

    if ($idlist) $where .= " and id_list=$idlist";
    if ($idcat) $where .= " and id_cat=$idcat";
    if ($iditem) $where .= " and id_item=$iditem";
    if ($idsub) $where .= " and id_sub=$idsub";
    if ($idbrand) $where .= " and id_brand=$idbrand";
    if ($comment_status == 'new') {
        $comment = $d->rawQuery("select distinct id_variant from #_comment where type = ? and find_in_set('new-admin',status)", array($type));
        $idcomment = (!empty($comment)) ? $func->joinCols($comment, 'id_variant') : 0;
        $where .= " and id in ($idcomment)";
    }
    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_product where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_product where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man" . $strUrl . "&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);

    /* Comment */
    $comment = new Comments($d, $func);
}

/* Edit man */
function editMan()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $item, $gallery, $type, $com, $act;

    if (!empty($_GET['id'])) $id = htmlspecialchars($_GET['id']);
    else if (!empty($_GET['id_copy'])) $id = htmlspecialchars($_GET['id_copy']);
    else $id = 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_product where id = ? and type = ? limit 0,1", array($id, $type));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
        } else {
            if ($act != 'copy') {
                /* Get gallery */
                $gallery = $d->rawQuery("select * from #_gallery where id_parent = ? and com = ? and type = ? and kind = ? and val = ? order by numb,id desc", array($id, $com, $type, 'man', $type));
            }
        }
    }
}

/* Save man */
function saveMan()
{
    global $d, $langadmin, $strUrl, $func, $flash, $curPage, $config, $com, $act, $type, $configBase, $setting;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $savehere = (isset($_POST['save-here'])) ? true : false;
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    $dataTags = (!empty($_POST['dataTags'])) ? $_POST['dataTags'] : null;
    $dataColor = (!empty($_POST['dataColor'])) ? $_POST['dataColor'] : null;
    $dataSize = (!empty($_POST['dataSize'])) ? $_POST['dataSize'] : null;
    $dataSpecifications = (!empty($_POST['dataSpecifications'])) ? $_POST['dataSpecifications'] : null;
    // dd($dataColor);
    // dd($dataSize);
    // dd($dataSpecifications);
    // die;
    if ($data) {
        foreach ($data as $column => $value) {
            if (strpos($column, 'content') !== false || strpos($column, 'desc') !== false) {
                $data[$column] = htmlspecialchars($func->sanitize($value, 'iframe'));
            } else {
                $data[$column] = htmlspecialchars($func->sanitize($value));
            }
        }

        if (!empty($config['product'][$type]['slug'])) {
            if (!empty($_POST['slugvi'])) $data['slugvi'] = $func->changeTitle(htmlspecialchars($_POST['slugvi']));
            else $data['slugvi'] = (!empty($data['namevi'])) ? $func->changeTitle($data['namevi']) : '';
            if (!empty($_POST['slugen'])) $data['slugen'] = $func->changeTitle(htmlspecialchars($_POST['slugen']));
            else $data['slugen'] = (!empty($data['nameen'])) ? $func->changeTitle($data['nameen']) : '';
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $data['regular_price'] = (isset($data['regular_price']) && $data['regular_price'] != '') ? str_replace(",", "", $data['regular_price']) : 0;
        $data['sale_price'] = (isset($data['sale_price']) && $data['sale_price'] != '') ? str_replace(",", "", $data['sale_price']) : 0;
        $data['discount'] = (isset($data['discount']) && $data['discount'] != '') ? $data['discount'] : 0;
        $data['type'] = $type;
    }

    $buildSchema = (isset($_POST['build-schema'])) ? true : false;

    /* Post seo */
    if (isset($config['product'][$type]['seo']) && $config['product'][$type]['seo'] == true) {
        $dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
        if ($dataSeo) {
            foreach ($dataSeo as $column => $value) {
                $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
            }
        }
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($config['product'][$type]['slug'])) {
        foreach ($config['website']['slug'] as $k => $v) {
            $dataSlug = array();
            $dataSlug['slug'] = $data['slug' . $k];
            $dataSlug['id'] = $id;
            $dataSlug['copy'] = ($act == 'save_copy') ? true : false;
            $checkSlug = $func->checkSlug($dataSlug);

            if ($checkSlug == 'exist') {
                $response['messages'][] = duongdandatontai;
            } else if ($checkSlug == 'empty') {
                $response['messages'][] = duongdankhongduoctrong;
            }
        }
    }

    if (!empty($data['regular_price']) && !$func->isNumber($data['regular_price'])) {
        $response['messages'][] = giabankhonghople;
    }

    if (!empty($data['sale_price']) && !$func->isNumber($data['sale_price'])) {
        $response['messages'][] = giamoikhonghople;
    }

    if (!empty($data['discount']) && !$func->isNumber($data['discount'])) {
        $response['messages'][] = chieckhaukhonghople;
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        if (!empty($dataSeo)) {
            foreach ($dataSeo as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id || $act == 'save_copy') {
            if ($act == 'save_copy') {
                $func->redirect("index.php?com=product&act=copy&type=" . $type . "&p=" . $curPage . $strUrl . "&id_copy=" . $id);
            } else {
                $func->redirect("index.php?com=product&act=edit&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id);
            }
        } else {
            $func->redirect("index.php?com=product&act=add&type=" . $type . "&p=" . $curPage . $strUrl);
        }
    }

    /* Save data */
    if ($id && $act != 'save_copy') {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('product', $data)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type'], UPLOAD_PRODUCT, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_product where id = ? and type = ? limit 0,1", array($id, $type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id);
                    $d->update('product', $photoUpdate);
                    unset($photoUpdate);
                }
            }
            if ($func->hasFile("file1")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file1"]["name"]);

                if ($icon = $func->uploadImage("file1", $config['product'][$type]['img_type'], UPLOAD_PRODUCT, $file_name)) {
                    $row = $d->rawQueryOne("select id, icon from #_product where id = ? and type = ? limit 0,1", array($id, $type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_PRODUCT . $row['icon']);
                    }
                    $photoUpdate['icon'] = $icon;
                    $d->where('id', $id);
                    $d->update('product', $photoUpdate);
                    unset($photoUpdate);
                }
            }
            /* SEO */
            if (isset($config['product'][$type]['seo']) && $config['product'][$type]['seo'] == true) {
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man', $type));

                $dataSeo['id_parent'] = $id;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            if (isset($config['product'][$type]['schema']) && $config['product'][$type]['schema'] == true) {
                //Kiểm tra nếu tạo Schema tự động
                if ($buildSchema) {
                    foreach ($config['website']['seo'] as $k => $v) {
                        //lấy tên danh mục
                        $pro_list = $d->rawQueryOne("select id,name$k as ten from #_product_list where id = ? and type = ? limit 0,1", array($data['id_list'], $type));
                        //lấy url thuộc vi,en 
                        if ($k == 'vi' || $k == 'en') {
                            $url_pro = $configBase . $data['slug' . $k];
                        } else {
                            $url_pro = $configBase . $data['slugvi'];
                        }
                        //Kiểm tra hình ảnh
                        $url_img_pro = array();
                        if (!empty($photoUpdate['photo'])) {
                            $url_img_pro[] = $configBase . UPLOAD_PRODUCT_L . $photoUpdate['photo'];
                        } else {
                            $img_pro = $d->rawQueryOne("select id, photo from #_product where id = ? and type = ? limit 0,1", array($id, $type));
                            $url_img_pro[] = $configBase . UPLOAD_PRODUCT_L . $img_pro['photo'];
                        }
                        $gallery = $d->rawQuery("select * from #_gallery where id_parent = ? and com = ? and type = ? and kind = ? and val = ? order by numb,id desc", array($id, $com, $type, 'man', $type));
                        if (!empty($gallery)) {
                            foreach ($gallery as $v_photo) {
                                $url_img_pro[] = $configBase . UPLOAD_PRODUCT_L . $v_photo['photo'];
                            }
                        }
                        $price = (!empty($data['sale_price'])) ? $data['sale_price'] : $data['regular_price'];
                        //Tiến hành build schema product
                        $dataSchema['schema' . $k] = $func->buildSchemaProduct($id, $data['name' . $k], $url_img_pro, $dataSeo['description' . $k], $data['code'], $pro_list['ten'], $setting['name' . $k], $url_pro, $price);
                    }
                } else {
                    $dataSchema = (isset($_POST['dataSchema'])) ? $_POST['dataSchema'] : null;
                    if ($dataSchema) {
                        foreach ($dataSchema as $column => $value) {
                            $dataSchema[$column] = htmlspecialchars($value);
                        }
                    }
                }
                $d->where('id_parent', $id);
                $d->where('com', $com);
                $d->where('act', 'man');
                $d->where('type', $type);
                $d->update('seo', $dataSchema);
            }

            /* Tags */
            if (isset($config['product'][$type]['tags']) && $config['product'][$type]['tags'] == true) {
                if ($dataTags) {
                    $d->rawQuery("delete from #_product_tags where id_parent = ?", array($id));
                    foreach ($dataTags as $v) {
                        $dataTag = array();
                        $dataTag['id_parent'] = $id;
                        $dataTag['id_tags'] = $v;
                        $d->insert('product_tags', $dataTag);
                    }
                } else {
                    $d->rawQuery("delete from #_product_tags where id_parent = ?", array($id));
                }
            }

            /* Sale */
            // dd($dataSpecifications);
            // dd($dataSize);
            // die;

            if (!empty($config['product'][$type]['color']) || !empty($config['product'][$type]['size']) || !empty($config['product'][$type]['specifications'])) {
                if (!empty($dataSize) && !empty($dataSpecifications)) {
                    $dataSale1 = array();
                    $dataSale1['id'] = 'id_color';
                    $dataSale1['data'] = $dataColor;

                    $dataSale2 = array();
                    $dataSale2['id'] = 'id_size';
                    $dataSale2['data'] = $dataSize;

                    $dataSale3 = array();
                    $dataSale3['id'] = 'id_specifications';
                    $dataSale3['data'] = $dataSpecifications;
                    // dd($dataSale3);
                    // die("123");
                } else if (!empty($dataSize)) {
                    $dataSale1 = array();
                    $dataSale1['id'] = 'id_size';
                    $dataSale1['data'] = $dataSize;
                } else if (!empty($dataSpecifications)) {
                    $dataSale1 = array();
                    $dataSale1['id'] = 'id_specifications';
                    $dataSale1['data'] = $dataSpecifications;
                    // dd($dataSale1);
                    // die;
                } 

                if (!empty($dataSale1['data']) || !empty($dataSale2['data']) || !empty($dataSale3['data'])) {
                    $d->rawQuery("delete from #_product_sale where id_parent = ?", array($id));

                    foreach ($dataSale2['data'] as $v_sale1) {
                        $dataSale = array();
                        $dataSale['id_parent'] = $id;
                        $dataSale[$dataSale2['id']] = $v_sale1;


                        if (!empty($dataSale3['data'])) {
                            foreach ($dataSale3['data'] as $v_sale3) {
                                $dataSale[$dataSale3['id']] = $v_sale3;
                                $d->insert('product_sale', $dataSale);
                            }
                        } else {
                            $d->insert('product_sale', $dataSale);
                        }
                    }
                } else {
                    $d->rawQuery("delete from #_product_sale where id_parent = ?", array($id));
                }
            }

            if ($savehere) {
                $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=edit&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id);
            } else {
                $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl);
            }
        } else {
            if ($savehere) {
                $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=edit&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id, false);
            } else {
                $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
            }
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('product', $data)) {
            $id_insert = $d->getLastInsertId();

            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file']["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type'], UPLOAD_PRODUCT, $file_name)) {
                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id_insert);
                    $d->update('product', $photoUpdate);
                    unset($photoUpdate);
                }
            }
            if ($func->hasFile("file1")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file1']["name"]);

                if ($icon = $func->uploadImage("file1", $config['product'][$type]['img_type'], UPLOAD_PRODUCT, $file_name)) {
                    $photoUpdate['icon'] = $icon;
                    $d->where('id', $id_insert);
                    $d->update('product', $photoUpdate);
                    unset($photoUpdate);
                }
            }
            /* SEO */
            if (isset($config['product'][$type]['seo']) && $config['product'][$type]['seo'] == true) {
                $dataSeo['id_parent'] = $id_insert;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            if (isset($config['product'][$type]['schema']) && $config['product'][$type]['schema'] == true) {
                //Kiểm tra nếu tạo Schema tự động
                if ($buildSchema) {
                    foreach ($config['website']['seo'] as $k => $v) {
                        //lấy tên danh mục
                        $pro_list = $d->rawQueryOne("select id,name$k as ten from #_product_list where id = ? and type = ? limit 0,1", array($data['id_list'], $type));
                        //lấy url thuộc vi,en 
                        if ($k == 'vi' || $k == 'en') {
                            $url_pro = $configBase . $data['slug' . $k];
                        } else {
                            $url_pro = $configBase . $data['slugvi'];
                        }
                        //Tiến hành build schema product
                        $price = (!empty($data['sale_price'])) ? $data['sale_price'] : $data['regular_price'];
                        $dataSchema['schema' . $k] = $func->buildSchemaProduct($id_insert, $data['name' . $k], $configBase . UPLOAD_PRODUCT_L . $photoUpdate['photo'], $dataSeo['description' . $k], $data['code'], $pro_list['ten'], $setting['name' . $k], $url_pro, $price);
                    }
                } else {
                    $dataSchema = (isset($_POST['dataSchema'])) ? $_POST['dataSchema'] : null;
                    if ($dataSchema) {
                        foreach ($dataSchema as $column => $value) {
                            $dataSchema[$column] = htmlspecialchars($value);
                        }
                    }
                }
                $d->where('id_parent', $id_insert);
                $d->where('com', $com);
                $d->where('act', 'man');
                $d->where('type', $type);
                $d->update('seo', $dataSchema);
            }

            /* Tags */
            if (isset($config['product'][$type]['tags']) && $config['product'][$type]['tags'] == true) {
                if ($dataTags) {
                    foreach ($dataTags as $v) {
                        $dataTag = array();
                        $dataTag['id_parent'] = $id_insert;
                        $dataTag['id_tags'] = $v;
                        $d->insert('product_tags', $dataTag);
                    }
                }
            }

            /* Sale */
            if (!empty($config['product'][$type]['color']) || !empty($config['product'][$type]['size'])) {
                if (!empty($dataColor) && !empty($dataSize)) {
                    $dataSale1 = array();
                    $dataSale1['id'] = 'id_color';
                    $dataSale1['data'] = $dataColor;

                    $dataSale2 = array();
                    $dataSale2['id'] = 'id_size';
                    $dataSale2['data'] = $dataSize;
                } else if (!empty($dataColor)) {
                    $dataSale1 = array();
                    $dataSale1['id'] = 'id_color';
                    $dataSale1['data'] = $dataColor;
                } else if (!empty($dataSize)) {
                    $dataSale1 = array();
                    $dataSale1['id'] = 'id_size';
                    $dataSale1['data'] = $dataSize;
                }

                if (!empty($dataSale1['data']) || !empty($dataSale2['data'])) {
                    foreach ($dataSale1['data'] as $v_sale1) {
                        $dataSale = array();
                        $dataSale['id_parent'] = $id_insert;
                        $dataSale[$dataSale1['id']] = $v_sale1;

                        if (!empty($dataSale2['data'])) {
                            foreach ($dataSale2['data'] as $v_sale2) {
                                $dataSale[$dataSale2['id']] = $v_sale2;
                                $d->insert('product_sale', $dataSale);
                            }
                        } else {
                            $d->insert('product_sale', $dataSale);
                        }
                    }
                }
            }

            /* Cập nhật hash khi upload multi */
            $hash = (isset($_POST['hash']) && $_POST['hash'] != '') ? addslashes($_POST['hash']) : null;
            if ($hash) {
                $d->rawQuery("update #_gallery set hash = ?, id_parent = ? where hash = ?", array('', $id_insert, $hash));
            }

            if ($act == 'save_copy') {
                if ($savehere || $buildSchema) {
                    $func->transfer(saochepdulieuthanhcong, "index.php?com=product&act=edit&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id_insert);
                } else {
                    $func->transfer(saochepdulieuthanhcong, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl);
                }
            } else {
                if ($savehere || $buildSchema) {
                    $func->transfer(luudulieuthanhcong, "index.php?com=product&act=edit&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id_insert);
                } else {
                    $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl);
                }
            }
        } else {
            if ($act == 'save_copy') {
                $func->transfer(saochepdulieubiloi, "index.php?com=product&act=copy&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id, false);
            } else {
                $func->transfer(luudulieubiloi, "index.php?com=product&act=copy&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id, false);
            }
        }
    }
}

/* Delete man */
function deleteMan()
{
    global $d, $langadmin, $strUrl, $func, $curPage, $com, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        /* Lấy dữ liệu */
        $row = $d->rawQueryOne("select id, photo, icon from #_product where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            /* Xóa chính */
            $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
            $func->deleteFile(UPLOAD_PRODUCT . $row['icon']);
            $d->rawQuery("delete from #_product where id = ?", array($id));

            /* Xóa SEO */
            $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man', $type));

            /* Xóa Tags */
            $d->rawQuery("delete from #_product_tags where id_parent = ?", array($id));

            /* Xóa Sale */
            $d->rawQuery("delete from #_product_sale where id_parent = ?", array($id));

            /* Xóa gallery */
            $rowGallery = $d->rawQuery("select id, photo, file_attach from #_gallery where id_parent = ? and kind = ? and com = ?", array($id, 'man', $com));

            if (count($rowGallery)) {
                foreach ($rowGallery as $v) {
                    $func->deleteFile(UPLOAD_PRODUCT . $v['photo']);
                    $func->deleteFile(UPLOAD_FILE . $v['file_attach']);
                }

                $d->rawQuery("delete from #_gallery where id_parent = ? and kind = ? and com = ?", array($id, 'man', $com));
            }

            /* Xóa comment */
            $rowComment = $d->rawQuery("select id, id_parent from #_comment where id_variant = ? and type = ?", array($id, $type));

            if (!empty($rowComment)) {
                foreach ($rowComment as $v) {
                    if ($v['id_parent'] == 0) {
                        /* Xóa comment photo */
                        $rowCommentPhoto = $d->rawQuery("select photo from #_comment_photo where id_parent = ?", array($v['id']));

                        if (!empty($rowCommentPhoto)) {
                            /* Xóa image */
                            foreach ($rowCommentPhoto as $v_photo) {
                                $func->deleteFile(UPLOAD_PHOTO . $v_photo['photo']);
                            }

                            /* Xóa photo */
                            $d->rawQuery("delete from #_comment_photo where id_parent = ?", array($v['id']));
                        }

                        /* Xóa comment video */
                        $rowCommentVideo = $d->rawQueryOne("select photo, video from #_comment_video where id_parent = ? limit 0,1", array($v['id']));

                        if (!empty($rowCommentVideo)) {
                            $func->deleteFile(UPLOAD_PHOTO . $rowCommentVideo['photo']);
                            $func->deleteFile(UPLOAD_VIDEO . $rowCommentVideo['video']);
                            $d->rawQuery("delete from #_comment_video where id_parent = ?", array($v['id']));
                        }

                        /* Xóa child */
                        $d->rawQuery("delete from #_comment where id_parent = ? and type = ?", array($v['id'], $type));
                    }

                    /* Xóa comment main */
                    $d->rawQuery("delete from #_comment where id = ? and type = ?", array($v['id'], $type));
                }
            }

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);

            /* Lấy dữ liệu */
            $row = $d->rawQueryOne("select id, photo, icon from #_product where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                /* Xóa chính */
                $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                $func->deleteFile(UPLOAD_PRODUCT . $row['icon']);
                $d->rawQuery("delete from #_product where id = ?", array($id));

                /* Xóa SEO */
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man', $type));

                /* Xóa Tags */
                $d->rawQuery("delete from #_product_tags where id_parent = ?", array($id));

                /* Xóa Sale */
                $d->rawQuery("delete from #_product_sale where id_parent = ?", array($id));

                /* Xóa gallery */
                $rowGallery = $d->rawQuery("select id, photo, file_attach from #_gallery where id_parent = ? and kind = ? and com = ?", array($id, 'man', $com));

                if (count($rowGallery)) {
                    foreach ($rowGallery as $v) {
                        $func->deleteFile(UPLOAD_PRODUCT . $v['photo']);
                        $func->deleteFile(UPLOAD_FILE . $v['file_attach']);
                    }

                    $d->rawQuery("delete from #_gallery where id_parent = ? and kind = ? and com = ?", array($id, 'man', $com));
                }

                /* Xóa comment */
                $rowComment = $d->rawQuery("select id, id_parent from #_comment where id_variant = ? and type = ?", array($id, $type));

                if (!empty($rowComment)) {
                    foreach ($rowComment as $v) {
                        if ($v['id_parent'] == 0) {
                            /* Xóa comment photo */
                            $rowCommentPhoto = $d->rawQuery("select photo from #_comment_photo where id_parent = ?", array($v['id']));

                            if (!empty($rowCommentPhoto)) {
                                /* Xóa image */
                                foreach ($rowCommentPhoto as $v_photo) {
                                    $func->deleteFile(UPLOAD_PHOTO . $v_photo['photo']);
                                }

                                /* Xóa photo */
                                $d->rawQuery("delete from #_comment_photo where id_parent = ?", array($v['id']));
                            }

                            /* Xóa comment video */
                            $rowCommentVideo = $d->rawQueryOne("select photo, video from #_comment_video where id_parent = ? limit 0,1", array($v['id']));

                            if (!empty($rowCommentVideo)) {
                                $func->deleteFile(UPLOAD_PHOTO . $rowCommentVideo['photo']);
                                $func->deleteFile(UPLOAD_VIDEO . $rowCommentVideo['video']);
                                $d->rawQuery("delete from #_comment_video where id_parent = ?", array($v['id']));
                            }

                            /* Xóa child */
                            $d->rawQuery("delete from #_comment where id_parent = ? and type = ?", array($v['id'], $type));
                        }

                        /* Xóa comment main */
                        $d->rawQuery("delete from #_comment where id = ? and type = ?", array($v['id'], $type));
                    }
                }
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }
}

/* View size */
function viewSizes()
{
    global $d, $langadmin, $func, $curPage, $items, $paging, $type;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_size where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_size where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man_size&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit size */
function editSize()
{
    global $d, $langadmin, $func, $curPage, $item, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage, false);
    } else {
        $item = $d->rawQueryOne("select * from #_size where id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage, false);
        }
    }
}

/* Save size */
function saveSize()
{
    global $d, $langadmin, $func, $flash, $curPage, $config, $type;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = htmlspecialchars($func->sanitize($value));
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $data['type'] = $type;
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=product&act=edit_size&type=" . $type . "&p=" . $curPage . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=product&act=add_size&type=" . $type . "&p=" . $curPage);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('size', $data)) {
            $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('size', $data)) {
            $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage, false);
        }
    }
}

/* Delete size */
function deleteSize()
{
    global $d, $langadmin, $func, $curPage, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQueryOne("select id from #_size where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            $d->rawQuery("delete from #_size where id = ? and type = ?", array($id, $type));

            /* Xóa size in Sale */
            $d->rawQuery("delete from #_product_sale where id_size = ?", array($id));

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQueryOne("select id from #_size where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                $d->rawQuery("delete from #_size where id = ? and type = ?", array($id, $type));

                /* Xóa size in Sale */
                $d->rawQuery("delete from #_product_sale where id_size = ?", array($id));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_size&type=" . $type . "&p=" . $curPage, false);
    }
}





/* View specifications */
function viewSpecifications()
{
    global $d, $langadmin, $func, $curPage, $items, $paging, $type;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_specifications where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_specifications where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man_specifications&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit size */
function editSpecifications()
{
    global $d, $langadmin, $func, $curPage, $item, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage, false);
    } else {
        $item = $d->rawQueryOne("select * from #_specifications where id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage, false);
        }
    }
}

/* Save size */
function saveSpecifications()
{
    global $d, $langadmin, $func, $flash, $curPage, $config, $type;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = htmlspecialchars($func->sanitize($value));
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $data['type'] = $type;
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=product&act=edit_specifications&type=" . $type . "&p=" . $curPage . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=product&act=add_specifications&type=" . $type . "&p=" . $curPage);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('specifications', $data)) {
            $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('specifications', $data)) {
            $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage, false);
        }
    }
}

/* Delete size */
function deleteSpecifications()
{
    global $d, $langadmin, $func, $curPage, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQueryOne("select id from #_specifications where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            $d->rawQuery("delete from #_specifications where id = ? and type = ?", array($id, $type));

            /* Xóa size in Sale */
            $d->rawQuery("delete from #_product_sale where id_specifications = ?", array($id));

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQueryOne("select id from #_specifications where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                $d->rawQuery("delete from #_specifications where id = ? and type = ?", array($id, $type));

                /* Xóa size in Sale */
                $d->rawQuery("delete from #_product_sale where id_specifications = ?", array($id));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_specifications&type=" . $type . "&p=" . $curPage, false);
    }
}

/* View color */
function viewColors()
{
    global $d, $langadmin, $func, $curPage, $items, $paging, $type;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_color where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_color where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man_color&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit color */
function editColor()
{
    global $d, $langadmin, $func, $curPage, $item, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage, false);
    } else {
        $item = $d->rawQueryOne("select * from #_color where id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage, false);
        }
    }
}

/* Save color */
function saveColor()
{
    global $d, $langadmin, $func, $flash, $curPage, $config, $type;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = htmlspecialchars($func->sanitize($value));
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $data['type'] = $type;
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=product&act=edit_color&type=" . $type . "&p=" . $curPage . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=product&act=add_color&type=" . $type . "&p=" . $curPage);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('color', $data)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_color'], UPLOAD_COLOR, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_color where id = ? and type = ? limit 0,1", array($id, $type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_COLOR . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id);
                    $d->update('color', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('color', $data)) {
            $id_insert = $d->getLastInsertId();

            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file']["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_color'], UPLOAD_COLOR, $file_name)) {
                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id_insert);
                    $d->update('color', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage, false);
        }
    }
}

/* Delete color */
function deleteColor()
{
    global $d, $langadmin, $curPage, $func, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQueryOne("select * from #_color where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            $func->deleteFile(UPLOAD_COLOR . $row['photo']);
            $d->rawQuery("delete from #_color where id = ?", array($id));

            /* Xóa color in Sale */
            $d->rawQuery("delete from #_product_sale where id_color = ?", array($id));

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQueryOne("select * from #_color where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                $func->deleteFile(UPLOAD_COLOR . $row['photo']);
                $d->rawQuery("delete from #_color where id = ?", array($id));

                /* Xóa color in Sale */
                $d->rawQuery("delete from #_product_sale where id_color = ?", array($id));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_color&type=" . $type . "&p=" . $curPage, false);
    }
}

/* View list */
function viewLists()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $items, $paging, $type;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_product_list where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_product_list where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man_list&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit list */
function editList()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $item, $gallery, $type, $com;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_product_list where id = ? and type = ? limit 0,1", array($id, $type));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl, false);
        } else {
            /* Get gallery */
            $gallery = $d->rawQuery("select * from #_gallery where id_parent = ? and com = ? and type = ? and kind = ? and val = ? order by numb,id desc", array($id, $com, $type, 'man_list', $type));
        }
    }
}

/* Save list */
function saveList()
{
    global $d, $langadmin, $strUrl, $func, $flash, $curPage, $config, $com, $type;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            if (strpos($column, 'content') !== false || strpos($column, 'desc') !== false) {
                $data[$column] = htmlspecialchars($func->sanitize($value, 'iframe'));
            } else {
                $data[$column] = htmlspecialchars($func->sanitize($value));
            }
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        if (!empty($config['product'][$type]['slug_list'])) {
            if (!empty($_POST['slugvi'])) $data['slugvi'] = $func->changeTitle(htmlspecialchars($_POST['slugvi']));
            else $data['slugvi'] = (!empty($data['namevi'])) ? $func->changeTitle($data['namevi']) : '';
            if (!empty($_POST['slugen'])) $data['slugen'] = $func->changeTitle(htmlspecialchars($_POST['slugen']));
            else $data['slugen'] = (!empty($data['nameen'])) ? $func->changeTitle($data['nameen']) : '';
        }

        $data['type'] = $type;
    }

    /* Post seo */
    if (isset($config['product'][$type]['seo_list']) && $config['product'][$type]['seo_list'] == true) {
        $dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
        if ($dataSeo) {
            foreach ($dataSeo as $column => $value) {
                $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
            }
        }
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($config['product'][$type]['slug_list'])) {
        foreach ($config['website']['slug'] as $k => $v) {
            $dataSlug = array();
            $dataSlug['slug'] = $data['slug' . $k];
            $dataSlug['id'] = $id;
            $dataSlug['copy'] = false;
            $checkSlug = $func->checkSlug($dataSlug);

            if ($checkSlug == 'exist') {
                $response['messages'][] = duongdandatontai;
            } else if ($checkSlug == 'empty') {
                $response['messages'][] = duongdankhongduoctrong;
            }
        }
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        if (!empty($dataSeo)) {
            foreach ($dataSeo as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=product&act=edit_list&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=product&act=add_list&type=" . $type . "&p=" . $curPage . $strUrl);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('product_list', $data)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_list'], UPLOAD_PRODUCT, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_product_list where id = ? and type = ? limit 0,1", array($id, $type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id);
                    $d->update('product_list', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_list']) && $config['product'][$type]['seo_list'] == true) {
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_list', $type));

                $dataSeo['id_parent'] = $id;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_list';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

             /* Build Schema Product for List - Using buildSchemaCategory */
            $shouldBuildSchema = isset($_POST['build-schema']);
            $rowList = $d->rawQueryOne(
                "select * from #_product_list where id = ? and type = ? limit 0,1",
                [$id, $type]
            );

            if ($shouldBuildSchema && !empty($rowList)) {
                global $configBase;

                // Lấy thông tin cơ bản
                $name = $rowList['namevi'] ?? $rowList['nameen'] ?? '';
                $descVi = $rowList['descvi'] ?? '';
                $descEn = $rowList['descen'] ?? '';
                $description = strip_tags($descVi) ?: strip_tags($descEn) ?: $name;
                $description = str_replace('"', '\"', $description);

                // Tạo URL cho category
                $url = $configBase . ($rowList['slugvi'] ?? '');

                // Lấy hình ảnh chính và gallery
                $image = [];
                if (!empty($rowList['photo'])) {
                    $image[] = $configBase . UPLOAD_PRODUCT_L . $rowList['photo'];
                }
                $gallery = $d->rawQuery(
                    "select photo from #_gallery where id_parent = ? and kind = ? and com = ? and photo != '' order by numb,id desc",
                    [$id, 'man_list', $com]
                );
                foreach ($gallery as $v) {
                    if (!empty($v['photo'])) {
                        $image[] = $configBase . UPLOAD_PRODUCT_L . $v['photo'];
                    }
                }
                if (empty($image)) {
                    $image[] = $configBase . 'assets/images/noimage.png';
                }

                // Build schema cho danh mục sử dụng phương thức buildSchemaCategory mới
                $schema = $func->buildSchemaCategory($id, $name, $image, $description, $url, $type, 'list', true);
                // Lưu schema vào SEO
                $d->rawQuery(
                    "update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?",
                    [$schema, $id, $com, 'man_list', $type]
                );
            } elseif (isset($_POST['dataSchema'])) {
                // Xử lý schema manual - sử dụng helper function mới
                $formattedSchema = $func->processSchemaFromPost($_POST['dataSchema']);
                
                if ($formattedSchema === false) {
                    // Nếu không thể format được, log error và giữ nguyên
                    error_log("Cannot format JSON schema for product list ID: $id");
                    $formattedSchema = is_string($_POST['dataSchema']) ? $_POST['dataSchema'] : json_encode($_POST['dataSchema']);
                }
                
                $d->rawQuery(
                    "update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?",
                    [$formattedSchema, $id, $com, 'man_list', $type]
                );
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('product_list', $data)) {
            $id_insert = $d->getLastInsertId();

            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file']["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_list'], UPLOAD_PRODUCT, $file_name)) {
                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id_insert);
                    $d->update('product_list', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_list']) && $config['product'][$type]['seo_list'] == true) {
                $dataSeo['id_parent'] = $id_insert;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_list';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            /* Build Schema Product for List */
            if (isset($_POST['build-schema']) || (isset($config['product'][$type]['schema']) && $config['product'][$type]['schema'] == true)) {
                $rowList = $d->rawQueryOne("select * from #_product_list where id = ? and type = ? limit 0,1", array($id_insert, $type));
                if (!empty($rowList)) {
                    global $configBase;
                    
                    // Lấy thông tin cơ bản
                    $name = !empty($rowList['namevi']) ? $rowList['namevi'] : $rowList['nameen'];
                    $description = !empty($rowList['descvi']) ? strip_tags($rowList['descvi']) : strip_tags($rowList['descen']);
                    $description = str_replace('"', '\"', $description);
                    
                    // Nếu không có description thì dùng name
                    if (empty($description)) {
                        $description = $name;
                    }
                    
                    // Tạo URL cho category
                    $url = $configBase . $rowList['slugvi'];
                    
                    // Lấy hình ảnh
                    $image = array();
                    if (!empty($rowList['photo'])) {
                        $image[] = $configBase . UPLOAD_PRODUCT_L . $rowList['photo'];
                    }
                    // Lấy gallery nếu có
                    $gallery = $d->rawQuery("select photo from #_gallery where id_parent = ? and kind = ? and com = ? and photo != '' order by numb,id desc", array($id_insert, 'man_list', $com));
                    if (count($gallery) > 0) {
                        foreach ($gallery as $v) {
                            $image[] = $configBase . UPLOAD_PRODUCT_L . $v['photo'];
                        }
                    }
                    
                    // Nếu không có hình ảnh nào, dùng hình mặc định
                    if (empty($image)) {
                        $image[] = $configBase . 'assets/images/noimage.png';
                    }
                    
                    // Build schema cho danh mục - tạo mới nên force rebuild = true
                    $schema = $func->buildSchemaCategory($id_insert, $name, $image, $description, $url, $type, 'list', true);
                    
                    // Lưu schema vào SEO
                    $d->rawQuery("update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?", array($schema, $id_insert, $com, 'man_list', $type));
                }
            }

            /* Cập nhật hash khi upload multi */
            $hash = (isset($_POST['hash']) && $_POST['hash'] != '') ? addslashes($_POST['hash']) : null;
            if ($hash) {
                $d->rawQuery("update #_gallery set hash = ?, id_parent = ? where hash = ?", array('', $id_insert, $hash));
            }

            $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Delete list */
function deleteList()
{
    global $d, $langadmin, $strUrl, $func, $curPage, $com, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        /* Lấy dữ liệu */
        $row = $d->rawQueryOne("select id, photo from #_product_list where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            /* Xóa chính */
            $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
            $d->rawQuery("delete from #_product_list where id = ?", array($id));

            /* Xóa SEO */
            $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_list', $type));

            /* Xóa gallery */
            $row = $d->rawQuery("select id, photo, file_attach from #_gallery where id_parent = ? and kind = ? and com = ?", array($id, 'man_list', $com));

            if (count($row)) {
                foreach ($row as $v) {
                    $func->deleteFile(UPLOAD_PRODUCT . $v['photo']);
                    $func->deleteFile(UPLOAD_FILE . $v['file_attach']);
                }

                $d->rawQuery("delete from #_gallery where id_parent = ? and kind = ? and com = ?", array($id, 'man_list', $com));
            }

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);

            /* Lấy dữ liệu */
            $row = $d->rawQueryOne("select id, photo from #_product_list where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                /* Xóa chính */
                $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                $d->rawQuery("delete from #_product_list where id = ?", array($id));

                /* Xóa SEO */
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_list', $type));

                /* Xóa gallery */
                $row = $d->rawQuery("select id, photo, file_attach from #_gallery where id_parent = ? and kind = ? and com = ?", array($id, 'man_list', $com));

                if (count($row)) {
                    foreach ($row as $v) {
                        $func->deleteFile(UPLOAD_PRODUCT . $v['photo']);
                        $func->deleteFile(UPLOAD_FILE . $v['file_attach']);
                    }

                    $d->rawQuery("delete from #_gallery where id_parent = ? and kind = ? and com = ?", array($id, 'man_list', $com));
                }
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_list&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }
}

/* Get cat */
function viewCats()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $items, $paging, $type;

    $where = "";
    $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;

    if ($idlist) $where .= " and id_list=$idlist";
    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_product_cat where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_product_cat where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man_cat" . $strUrl . "&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit cat */
function editCat()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $item, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_product_cat where id = ? and type = ? limit 0,1", array($id, $type));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Save cat */
function saveCat()
{
    global $d, $langadmin, $strUrl, $func, $flash, $curPage, $config, $com, $type, $configBase;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            if (strpos($column, 'content') !== false || strpos($column, 'desc') !== false) {
                $data[$column] = htmlspecialchars($func->sanitize($value, 'iframe'));
            } else {
                $data[$column] = htmlspecialchars($func->sanitize($value));
            }
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        if (!empty($config['product'][$type]['slug_cat'])) {
            if (!empty($_POST['slugvi'])) $data['slugvi'] = $func->changeTitle(htmlspecialchars($_POST['slugvi']));
            else $data['slugvi'] = (!empty($data['namevi'])) ? $func->changeTitle($data['namevi']) : '';
            if (!empty($_POST['slugen'])) $data['slugen'] = $func->changeTitle(htmlspecialchars($_POST['slugen']));
            else $data['slugen'] = (!empty($data['nameen'])) ? $func->changeTitle($data['nameen']) : '';
        }

        $data['type'] = $type;
    }

    /* Post seo */
    if (isset($config['product'][$type]['seo_cat']) && $config['product'][$type]['seo_cat'] == true) {
        $dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
        if ($dataSeo) {
            foreach ($dataSeo as $column => $value) {
                $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
            }
        }
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($config['product'][$type]['slug_cat'])) {
        foreach ($config['website']['slug'] as $k => $v) {
            $dataSlug = array();
            $dataSlug['slug'] = $data['slug' . $k];
            $dataSlug['id'] = $id;
            $dataSlug['copy'] = false;
            $checkSlug = $func->checkSlug($dataSlug);

            if ($checkSlug == 'exist') {
                $response['messages'][] = duongdandatontai;
            } else if ($checkSlug == 'empty') {
                $response['messages'][] = duongdankhongduoctrong;
            }
        }
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        if (!empty($dataSeo)) {
            foreach ($dataSeo as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=product&act=edit_cat&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=product&act=add_cat&type=" . $type . "&p=" . $curPage . $strUrl);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('product_cat', $data)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_cat'], UPLOAD_PRODUCT, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_product_cat where id = ? and type = ? limit 0,1", array($id, $type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id);
                    $d->update('product_cat', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_cat']) && $config['product'][$type]['seo_cat'] == true) {
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_cat', $type));

                $dataSeo['id_parent'] = $id;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_cat';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            /* Build Schema Product for Cat - Using buildSchemaCategory */
            $shouldBuildSchema = isset($_POST['build-schema']);
            $rowCat = $d->rawQueryOne(
                "select * from #_product_cat where id = ? and type = ? limit 0,1",
                [$id, $type]
            );

            if ($shouldBuildSchema && !empty($rowCat)) {
                // Lấy thông tin cơ bản
                $name = $rowCat['namevi'] ?? $rowCat['nameen'] ?? '';
                $descVi = $rowCat['descvi'] ?? '';
                $descEn = $rowCat['descen'] ?? '';
                $description = strip_tags($descVi) ?: strip_tags($descEn) ?: $name;
                $description = str_replace('"', '\"', $description);

                // Tạo URL cho category
                $url = $configBase . ($rowCat['slugvi'] ?? '');

                // Lấy hình ảnh chính và gallery
                $image = [];
                if (!empty($rowCat['photo'])) {
                    $image[] = $configBase . UPLOAD_PRODUCT_L . $rowCat['photo'];
                }
                $gallery = $d->rawQuery(
                    "select photo from #_gallery where id_parent = ? and kind = ? and com = ? and photo != '' order by numb,id desc",
                    [$id, 'man_cat', $com]
                );
                foreach ($gallery as $v) {
                    if (!empty($v['photo'])) {
                        $image[] = $configBase . UPLOAD_PRODUCT_L . $v['photo'];
                    }
                }
                if (empty($image)) {
                    $image[] = $configBase . 'assets/images/noimage.png';
                }

                // Build schema cho danh mục sử dụng phương thức buildSchemaCategory mới
                $schema = $func->buildSchemaCategory($id, $name, $image, $description, $url, $type, 'cat', true);
                // Lưu schema vào SEO
                $d->rawQuery(
                    "update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?",
                    [$schema, $id, $com, 'man_cat', $type]
                );
            } elseif (isset($_POST['dataSchema'])) {
                // Xử lý schema manual - sử dụng helper function mới
                $formattedSchema = $func->processSchemaFromPost($_POST['dataSchema']);
                
                if ($formattedSchema === false) {
                    // Nếu không thể format được, log error và giữ nguyên
                    error_log("Cannot format JSON schema for product cat ID: $id");
                    $formattedSchema = is_string($_POST['dataSchema']) ? $_POST['dataSchema'] : json_encode($_POST['dataSchema']);
                }
                
                $d->rawQuery(
                    "update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?",
                    [$formattedSchema, $id, $com, 'man_cat', $type]
                );
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('product_cat', $data)) {
            $id_insert = $d->getLastInsertId();

            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file']["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_cat'], UPLOAD_PRODUCT, $file_name)) {
                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id_insert);
                    $d->update('product_cat', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_cat']) && $config['product'][$type]['seo_cat'] == true) {
                $dataSeo['id_parent'] = $id_insert;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_cat';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            /* Build Schema Product for Cat */
            if (isset($_POST['build-schema']) || (isset($config['product'][$type]['schema']) && $config['product'][$type]['schema'] == true)) {
                $rowCat = $d->rawQueryOne("select * from #_product_cat where id = ? and type = ? limit 0,1", array($id_insert, $type));
                if (!empty($rowCat)) {
                    // Lấy thông tin cơ bản
                    $name = !empty($rowCat['namevi']) ? $rowCat['namevi'] : $rowCat['nameen'];
                    $description = !empty($rowCat['descvi']) ? strip_tags($rowCat['descvi']) : strip_tags($rowCat['descen']);
                    $description = str_replace('"', '\"', $description);
                    
                    // Nếu không có description thì dùng name
                    if (empty($description)) {
                        $description = $name;
                    }
                    
                    // Tạo URL cho category
                    $url = $configBase . $rowCat['slugvi'];
                    
                    // Lấy hình ảnh
                    $image = array();
                    if (!empty($rowCat['photo'])) {
                        $image[] = $configBase . UPLOAD_PRODUCT_L . $rowCat['photo'];
                    }
                    // Lấy gallery nếu có
                    $gallery = $d->rawQuery("select photo from #_gallery where id_parent = ? and kind = ? and com = ? and photo != '' order by numb,id desc", array($id_insert, 'man_cat', $com));
                    if (count($gallery) > 0) {
                        foreach ($gallery as $v) {
                            $image[] = $configBase . UPLOAD_PRODUCT_L . $v['photo'];
                        }
                    }
                    
                    // Nếu không có hình ảnh nào, dùng hình mặc định
                    if (empty($image)) {
                        $image[] = $configBase . 'assets/images/noimage.png';
                    }
                    
                    // Build schema cho danh mục - tạo mới nên force rebuild = true
                    $schema = $func->buildSchemaCategory($id_insert, $name, $image, $description, $url, $type, 'cat', true);
                    
                    // Lưu schema vào SEO
                    $d->rawQuery("update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?", array($schema, $id_insert, $com, 'man_cat', $type));
                }
            }

            $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Delete cat */
function deleteCat()
{
    global $d, $langadmin, $strUrl, $func, $curPage, $com, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        /* Lấy dữ liệu */
        $row = $d->rawQueryOne("select id, photo from #_product_cat where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            /* Xóa chính */
            $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
            $d->rawQuery("delete from #_product_cat where id = ?", array($id));

            /* Xóa SEO */
            $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_cat', $type));

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);

            /* Lấy dữ liệu */
            $row = $d->rawQueryOne("select id, photo from #_product_cat where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                /* Xóa chính */
                $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                $d->rawQuery("delete from #_product_cat where id = ?", array($id));

                /* Xóa SEO */
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_cat', $type));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_cat&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }
}

/* View item */
function viewItems()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $items, $paging, $type;

    $where = "";
    $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
    $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;

    if ($idlist) $where .= " and id_list=$idlist";
    if ($idcat) $where .= " and id_cat=$idcat";
    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_product_item where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_product_item where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man_item" . $strUrl . "&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit item */
function editItem()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $item, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_product_item where id = ? and type = ? limit 0,1", array($id, $type));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Save item */
function saveItem()
{
    global $d, $langadmin, $strUrl, $func, $flash, $curPage, $config, $com, $type, $configBase;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            if (strpos($column, 'content') !== false || strpos($column, 'desc') !== false) {
                $data[$column] = htmlspecialchars($func->sanitize($value, 'iframe'));
            } else {
                $data[$column] = htmlspecialchars($func->sanitize($value));
            }
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        if (!empty($config['product'][$type]['slug_item'])) {
            if (!empty($_POST['slugvi'])) $data['slugvi'] = $func->changeTitle(htmlspecialchars($_POST['slugvi']));
            else $data['slugvi'] = (!empty($data['namevi'])) ? $func->changeTitle($data['namevi']) : '';
            if (!empty($_POST['slugen'])) $data['slugen'] = $func->changeTitle(htmlspecialchars($_POST['slugen']));
            else $data['slugen'] = (!empty($data['nameen'])) ? $func->changeTitle($data['nameen']) : '';
        }

        $data['type'] = $type;
    }

    /* Post seo */
    if (isset($config['product'][$type]['seo_item']) && $config['product'][$type]['seo_item'] == true) {
        $dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
        if ($dataSeo) {
            foreach ($dataSeo as $column => $value) {
                $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
            }
        }
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($config['product'][$type]['slug_item'])) {
        foreach ($config['website']['slug'] as $k => $v) {
            $dataSlug = array();
            $dataSlug['slug'] = $data['slug' . $k];
            $dataSlug['id'] = $id;
            $dataSlug['copy'] = false;
            $checkSlug = $func->checkSlug($dataSlug);

            if ($checkSlug == 'exist') {
                $response['messages'][] = duongdandatontai;
            } else if ($checkSlug == 'empty') {
                $response['messages'][] = duongdankhongduoctrong;
            }
        }
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        if (!empty($dataSeo)) {
            foreach ($dataSeo as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=product&act=edit_item&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=product&act=add_item&type=" . $type . "&p=" . $curPage . $strUrl);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('product_item', $data)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_item'], UPLOAD_PRODUCT, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_product_item where id = ? and type = ? limit 0,1", array($id, $type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id);
                    $d->update('product_item', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_item']) && $config['product'][$type]['seo_item'] == true) {
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_item', $type));

                $dataSeo['id_parent'] = $id;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_item';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            /* Build Schema Product for Item - Using buildSchemaCategory */
            $shouldBuildSchema = isset($_POST['build-schema']);
            $rowItem = $d->rawQueryOne(
                "select * from #_product_item where id = ? and type = ? limit 0,1",
                [$id, $type]
            );

            if ($shouldBuildSchema && !empty($rowItem)) {
                // Lấy thông tin cơ bản
                $name = $rowItem['namevi'] ?? $rowItem['nameen'] ?? '';
                $descVi = $rowItem['descvi'] ?? '';
                $descEn = $rowItem['descen'] ?? '';
                $description = strip_tags($descVi) ?: strip_tags($descEn) ?: $name;
                $description = str_replace('"', '\"', $description);

                // Tạo URL cho category
                $url = $configBase . ($rowItem['slugvi'] ?? '');

                // Lấy hình ảnh chính và gallery
                $image = [];
                if (!empty($rowItem['photo'])) {
                    $image[] = $configBase . UPLOAD_PRODUCT_L . $rowItem['photo'];
                }
                $gallery = $d->rawQuery(
                    "select photo from #_gallery where id_parent = ? and kind = ? and com = ? and photo != '' order by numb,id desc",
                    [$id, 'man_item', $com]
                );
                foreach ($gallery as $v) {
                    if (!empty($v['photo'])) {
                        $image[] = $configBase . UPLOAD_PRODUCT_L . $v['photo'];
                    }
                }
                if (empty($image)) {
                    $image[] = $configBase . 'assets/images/noimage.png';
                }

                // Build schema cho danh mục sử dụng phương thức buildSchemaCategory mới
                $schema = $func->buildSchemaCategory($id, $name, $image, $description, $url, $type, 'item', true);
                // Lưu schema vào SEO
                $d->rawQuery(
                    "update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?",
                    [$schema, $id, $com, 'man_item', $type]
                );
            } elseif (isset($_POST['dataSchema'])) {
                // Xử lý schema manual - sử dụng helper function mới
                $formattedSchema = $func->processSchemaFromPost($_POST['dataSchema']);
                
                if ($formattedSchema === false) {
                    // Nếu không thể format được, log error và giữ nguyên
                    error_log("Cannot format JSON schema for product item ID: $id");
                    $formattedSchema = is_string($_POST['dataSchema']) ? $_POST['dataSchema'] : json_encode($_POST['dataSchema']);
                }
                
                $d->rawQuery(
                    "update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?",
                    [$formattedSchema, $id, $com, 'man_item', $type]
                );
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('product_item', $data)) {
            $id_insert = $d->getLastInsertId();

            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file']["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_item'], UPLOAD_PRODUCT, $file_name)) {
                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id_insert);
                    $d->update('product_item', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_item']) && $config['product'][$type]['seo_item'] == true) {
                $dataSeo['id_parent'] = $id_insert;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_item';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            /* Build Schema Product for Item */
            if (isset($_POST['build-schema']) || (isset($config['product'][$type]['schema']) && $config['product'][$type]['schema'] == true)) {
                $rowItem = $d->rawQueryOne("select * from #_product_item where id = ? and type = ? limit 0,1", array($id_insert, $type));
                if (!empty($rowItem)) {
                    // Lấy thông tin cơ bản
                    $name = !empty($rowItem['namevi']) ? $rowItem['namevi'] : $rowItem['nameen'];
                    $description = !empty($rowItem['descvi']) ? strip_tags($rowItem['descvi']) : strip_tags($rowItem['descen']);
                    $description = str_replace('"', '\"', $description);
                    
                    // Nếu không có description thì dùng name
                    if (empty($description)) {
                        $description = $name;
                    }
                    
                    // Tạo URL cho category
                    $url = $configBase . $rowItem['slugvi'];
                    
                    // Lấy hình ảnh
                    $image = array();
                    if (!empty($rowItem['photo'])) {
                        $image[] = $configBase . UPLOAD_PRODUCT_L . $rowItem['photo'];
                    }
                    // Lấy gallery nếu có
                    $gallery = $d->rawQuery("select photo from #_gallery where id_parent = ? and kind = ? and com = ? and photo != '' order by numb,id desc", array($id_insert, 'man_item', $com));
                    if (count($gallery) > 0) {
                        foreach ($gallery as $v) {
                            $image[] = $configBase . UPLOAD_PRODUCT_L . $v['photo'];
                        }
                    }
                    
                    // Nếu không có hình ảnh nào, dùng hình mặc định
                    if (empty($image)) {
                        $image[] = $configBase . 'assets/images/noimage.png';
                    }
                    
                    // Build schema cho danh mục - tạo mới nên force rebuild = true
                    $schema = $func->buildSchemaCategory($id_insert, $name, $image, $description, $url, $type, 'item', true);
                    
                    // Lưu schema vào SEO
                    $d->rawQuery("update #_seo set `schemavi` = ? where id_parent = ? and com = ? and act = ? and type = ?", array($schema, $id_insert, $com, 'man_item', $type));
                }
            }

            $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Delete item */
function deleteItem()
{
    global $d, $langadmin, $strUrl, $func, $curPage, $com, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        /* Lấy dữ liệu */
        $row = $d->rawQueryOne("select id, photo from #_product_item where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            /* Xóa chính */
            $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
            $d->rawQuery("delete from #_product_item where id = ?", array($id));

            /* Xóa SEO */
            $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_item', $type));

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);

            /* Lấy dữ liệu */
            $row = $d->rawQueryOne("select id, photo from #_product_item where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                /* Xóa chính */
                $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                $d->rawQuery("delete from #_product_item where id = ?", array($id));

                /* Xóa SEO */
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_item', $type));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_item&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }
}

/* View sub */
function viewSubs()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $items, $paging, $type;

    $where = "";

    $idlist = (isset($_REQUEST['id_list'])) ? htmlspecialchars($_REQUEST['id_list']) : 0;
    $idcat = (isset($_REQUEST['id_cat'])) ? htmlspecialchars($_REQUEST['id_cat']) : 0;
    $iditem = (isset($_REQUEST['id_item'])) ? htmlspecialchars($_REQUEST['id_item']) : 0;

    if ($idlist) $where .= " and id_list=$idlist";
    if ($idcat) $where .= " and id_cat=$idcat";
    if ($iditem) $where .= " and id_item=$iditem";
    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_product_sub where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_product_sub where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man_sub" . $strUrl . "&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit sub */
function editSub()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $item, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_product_sub where id = ? and type = ? limit 0,1", array($id, $type));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Save sub */
function saveSub()
{
    global $d, $langadmin, $strUrl, $func, $flash, $curPage, $config, $com, $type;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            if (strpos($column, 'content') !== false || strpos($column, 'desc') !== false) {
                $data[$column] = htmlspecialchars($func->sanitize($value, 'iframe'));
            } else {
                $data[$column] = htmlspecialchars($func->sanitize($value));
            }
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        if (!empty($config['product'][$type]['slug_sub'])) {
            if (!empty($_POST['slugvi'])) $data['slugvi'] = $func->changeTitle(htmlspecialchars($_POST['slugvi']));
            else $data['slugvi'] = (!empty($data['namevi'])) ? $func->changeTitle($data['namevi']) : '';
            if (!empty($_POST['slugen'])) $data['slugen'] = $func->changeTitle(htmlspecialchars($_POST['slugen']));
            else $data['slugen'] = (!empty($data['nameen'])) ? $func->changeTitle($data['nameen']) : '';
        }

        $data['type'] = $type;
    }

    /* Post seo */
    if (isset($config['product'][$type]['seo_sub']) && $config['product'][$type]['seo_sub'] == true) {
        $dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
        if ($dataSeo) {
            foreach ($dataSeo as $column => $value) {
                $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
            }
        }
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($config['product'][$type]['slug_sub'])) {
        foreach ($config['website']['slug'] as $k => $v) {
            $dataSlug = array();
            $dataSlug['slug'] = $data['slug' . $k];
            $dataSlug['id'] = $id;
            $dataSlug['copy'] = false;
            $checkSlug = $func->checkSlug($dataSlug);

            if ($checkSlug == 'exist') {
                $response['messages'][] = duongdandatontai;
            } else if ($checkSlug == 'empty') {
                $response['messages'][] = duongdankhongduoctrong;
            }
        }
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        if (!empty($dataSeo)) {
            foreach ($dataSeo as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=product&act=edit_sub&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=product&act=add_sub&type=" . $type . "&p=" . $curPage . $strUrl);
        }
    }

    /* save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('product_sub', $data)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_sub'], UPLOAD_PRODUCT, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_product_sub where id = ? and type = ? limit 0,1", array($id, $type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id);
                    $d->update('product_sub', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_sub']) && $config['product'][$type]['seo_sub'] == true) {
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_sub', $type));

                $dataSeo['id_parent'] = $id;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_sub';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('product_sub', $data)) {
            $id_insert = $d->getLastInsertId();

            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file']["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_sub'], UPLOAD_PRODUCT, $file_name)) {
                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id_insert);
                    $d->update('product_sub', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_sub']) && $config['product'][$type]['seo_sub'] == true) {
                $dataSeo['id_parent'] = $id_insert;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_sub';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Delete sub */
function deleteSub()
{
    global $d, $langadmin, $strUrl, $func, $curPage, $com, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        /* Lấy dữ liệu */
        $row = $d->rawQueryOne("select id, photo from #_product_sub where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            /* Xóa chính */
            $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
            $d->rawQuery("delete from #_product_sub where id = ?", array($id));

            /* Xóa SEO */
            $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_sub', $type));

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);

            /* Lấy dữ liệu */
            $row = $d->rawQueryOne("select id, photo from #_product_sub where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                /* Xóa chính */
                $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                $d->rawQuery("delete from #_product_sub where id = ?", array($id));

                /* Xóa SEO */
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_sub', $type));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_sub&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }
}

/* View brand */
function viewBrands()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $items, $paging, $type;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (namevi LIKE '%$keyword%' or nameen LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_product_brand where type = ? $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql, array($type));
    $sqlNum = "select count(*) as 'num' from #_product_brand where type = ? $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum, array($type));
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=product&act=man_brand&type=" . $type;
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit brand */
function editBrand()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $item, $gallery, $type, $com;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_product_brand where id = ? and type = ? limit 0,1", array($id, $type));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Save brand */
function saveBrand()
{
    global $d, $langadmin, $curPage, $func, $flash, $config, $com, $type, $strUrl;

    /* Check post*/
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            if (strpos($column, 'content') !== false || strpos($column, 'desc') !== false) {
                $data[$column] = htmlspecialchars($func->sanitize($value, 'iframe'));
            } else {
                $data[$column] = htmlspecialchars($func->sanitize($value));
            }
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        if (!empty($config['product'][$type]['slug_brand'])) {
            if (!empty($_POST['slugvi'])) $data['slugvi'] = $func->changeTitle(htmlspecialchars($_POST['slugvi']));
            else $data['slugvi'] = (!empty($data['namevi'])) ? $func->changeTitle($data['namevi']) : '';
            if (!empty($_POST['slugen'])) $data['slugen'] = $func->changeTitle(htmlspecialchars($_POST['slugen']));
            else $data['slugen'] = (!empty($data['nameen'])) ? $func->changeTitle($data['nameen']) : '';
        }

        $data['type'] = $type;
    }

    /* Post seo */
    if (isset($config['product'][$type]['seo_brand']) && $config['product'][$type]['seo_brand'] == true) {
        $dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
        if ($dataSeo) {
            foreach ($dataSeo as $column => $value) {
                $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
            }
        }
    }

    /* Valid data */
    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
    }

    if (!empty($config['product'][$type]['slug_brand'])) {
        foreach ($config['website']['slug'] as $k => $v) {
            $dataSlug = array();
            $dataSlug['slug'] = $data['slug' . $k];
            $dataSlug['id'] = $id;
            $dataSlug['copy'] = false;
            $checkSlug = $func->checkSlug($dataSlug);

            if ($checkSlug == 'exist') {
                $response['messages'][] = duongdandatontai;
            } else if ($checkSlug == 'empty') {
                $response['messages'][] = duongdankhongduoctrong;
            }
        }
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        if (!empty($dataSeo)) {
            foreach ($dataSeo as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=product&act=edit_brand&type=" . $type . "&p=" . $curPage . $strUrl . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=product&act=add_brand&type=" . $type . "&p=" . $curPage . $strUrl);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        $d->where('type', $type);
        if ($d->update('product_brand', $data)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_brand'], UPLOAD_PRODUCT, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_product_brand where id = ? and type = ? limit 0,1", array($id, $type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id);
                    $d->update('product_brand', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_brand']) && $config['product'][$type]['seo_brand'] == true) {
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_brand', $type));

                $dataSeo['id_parent'] = $id;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_brand';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('product_brand', $data)) {
            $id_insert = $d->getLastInsertId();

            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file']["name"]);

                if ($photo = $func->uploadImage("file", $config['product'][$type]['img_type_brand'], UPLOAD_PRODUCT, $file_name)) {
                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id_insert);
                    $d->update('product_brand', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* SEO */
            if (isset($config['product'][$type]['seo_brand']) && $config['product'][$type]['seo_brand'] == true) {
                $dataSeo['id_parent'] = $id_insert;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'man_brand';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            $func->transfer(luudulieuthanhcong, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage, false);
        }
    }
}

/* Delete brand */
function deleteBrand()
{
    global $d, $langadmin, $strUrl, $func, $curPage, $com, $type;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        /* Lấy dữ liệu */
        $row = $d->rawQueryOne("select id, photo from #_product_brand where id = ? and type = ? limit 0,1", array($id, $type));

        if (!empty($row)) {
            /* Xóa chính */
            $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
            $d->rawQuery("delete from #_product_brand where id = ?", array($id));

            /* Xóa SEO */
            $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_brand', $type));

            $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);

            /* Lấy dữ liệu */
            $row = $d->rawQueryOne("select id, photo from #_product_brand where id = ? and type = ? limit 0,1", array($id, $type));

            if (!empty($row)) {
                /* Xóa chính */
                $func->deleteFile(UPLOAD_PRODUCT . $row['photo']);
                $d->rawQuery("delete from #_product_brand where id = ?", array($id));

                /* Xóa SEO */
                $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array($id, $com, 'man_brand', $type));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=product&act=man_brand&type=" . $type . "&p=" . $curPage . $strUrl, false);
    }
}
