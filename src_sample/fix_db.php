<?php
session_start();
define('LIBRARIES', './libraries/');
define('SOURCES', './sources/');

/* Config */
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';

new AutoLoad();
$d = new PDODb($config['database']);

echo "Checking database structure...<br>";

// Check and add columns
$updates = [
    'link2' => "ALTER TABLE #_photo ADD COLUMN link2 TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL",
    'loaidieuhuong' => "ALTER TABLE #_photo ADD COLUMN loaidieuhuong INT(1) DEFAULT 0"
];

foreach ($updates as $col => $sql) {
    try {
        $check = $d->rawQueryOne("SHOW COLUMNS FROM #_photo LIKE '$col'");
        if (!$check) {
            $d->rawQuery($sql);
            echo "- Added column <b>$col</b> successfully.<br>";
        } else {
            echo "- Column <b>$col</b> already exists.<br>";
        }
    } catch (Exception $e) {
        echo "- Error checking/adding $col: " . $e->getMessage() . "<br>";
    }
}

echo "<br>Done. Please delete this file after use.";
?>