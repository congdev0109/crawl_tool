<!-- Open Graph / Facebook -->
<meta property="og:type" content="<?= $seo->get('type') ?>">
<meta property="og:url" content="<?= $seo->get('url') ?>">
<meta property="og:title" content="<?= $seo->get('title') ?>">
<meta property="og:description" content="<?= $seo->get('description') ?>">
<?php if ($seo->get('photo')) { ?>
<meta property="og:image" content="<?= $seo->get('photo') ?>">
<?php } ?>

<!-- JSON-LD Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "<?= $optsetting['ten-cong-ty'] ?>",
    "url": "<?= $configBase ?>",
    "logo": "<?= $configBase ?><?= THUMBS ?>/120x100x1/<?= UPLOAD_PHOTO_L . $logo['photo'] ?>",
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?= $optsetting['hotline'] ?>",
        "contactType": "Customer Service"
    }
}
</script>