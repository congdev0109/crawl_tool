<?php
include "config.php";

$id_city = (!empty($_POST['id_city'])) ? htmlspecialchars($_POST['id_city']) : 0;
$ward = null;
if ($id_city) $ward = $d->rawQuery("select name, id from #_ward where id_city = ? and find_in_set('hienthi',status) order by numb asc", array($id_city));

if ($ward) { ?>
    <option value=""><?= phuongxa ?></option>
    <?php foreach ($ward as $k => $v) { ?>
        <option value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
    <?php }
} else { ?>
    <option value=""><?= phuongxa ?></option>
<?php }
?>