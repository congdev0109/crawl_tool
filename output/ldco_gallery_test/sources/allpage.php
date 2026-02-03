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
