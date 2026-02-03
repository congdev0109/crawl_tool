<header class="header">
    <div class="header-top">
        <div class="wrap-content d-flex flex-wrap justify-content-between align-items-center">
            <div class="header-contact d-flex align-items-center">
                <p class="info-header"><i class="bi bi-envelope"></i> Email: <?= $optsetting['email'] ?></p>
                <p class="info-header ms-3"><i class="bi bi-telephone"></i> Hotline: <?= $optsetting['hotline'] ?></p>
            </div>
            <?php if (!empty($social)) { ?>
            <div class="header-social d-flex align-items-center">
                <?php foreach ($social as $v) { ?>
                    <a href="<?= $v['link'] ?>" target="_blank" title="<?= $v['namevi'] ?>">
                        <img src="<?= THUMBS ?>/40x40x1/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['namevi'] ?>">
                    </a>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="header-main">
        <div class="wrap-content d-flex align-items-center justify-content-between">
            <a class="logo" href="">
                <img onerror="this.src='<?= THUMBS ?>/326x88x2/assets/images/noimage.png';" 
                     src="<?= THUMBS ?>/326x88x2/<?= UPLOAD_PHOTO_L . $logo['photo'] ?>" 
                     alt="logo" title="logo" />
            </a>
            <div class="header-info">
                <p class="hotline">Hotline: <?= $optsetting['hotline'] ?></p>
            </div>
        </div>
    </div>
</header>