<?php
include "config.php";

if (!empty($_POST["table"])) {
    $table = htmlspecialchars($_POST["table"]);
    $id_city = !empty($_POST["id_city"]) ? (int)$_POST["id_city"] : 0;
    $options = "<option value=''>Chọn danh mục</option>";

    if ($table == 'ward' && $id_city) {
        $wards = $d->rawQuery("SELECT id, name FROM #_ward WHERE id_city = ? ORDER BY name ASC", [$id_city]);
        foreach ($wards as $ward) {
            $options .= "<option value='" . $ward['id'] . "'>" . $ward['name'] . "</option>";
        }
        echo $options;
        exit();
    }
    // Nếu table là city, trả về toàn bộ city
    if ($table == 'city') {
        $citys = $d->rawQuery("SELECT id, name FROM #_city ORDER BY name ASC");
        foreach ($citys as $city) {
            $options .= "<option value='" . $city['id'] . "'>" . $city['name'] . "</option>";
        }
        echo $options;
        exit();
    }
    // Nếu không hợp lệ
    echo $options;
    exit();
}
echo 'error ajax';
