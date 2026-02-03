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
