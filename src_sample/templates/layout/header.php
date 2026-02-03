<div class="head">
    <div class="head-top">
        <div class="wrap-content d-flex flex-wrap justify-content-between align-items-center ">
            <p class="slogan-head mb-0 ">
                <marquee><?= $slogan['name' . $lang] ?></marquee>
            </p>
            <div class=" d-flex align-items-center ">
                <p class="info-head"><i class="bi bi-envelope"></i> Email: <?= $optsetting['email'] ?></p>
                <p class="info-head ms-2 me-2"><i class="bi bi-telephone"></i> Hotline: <?= $func->formatPhone($optsetting['hotline']) ?></p>
                <div class="lang-head me-2 ">
                    <a href="ngon-ngu/vi/"><img src="<?= THUMBS ?>/30x20x1/assets/images/vi.jpg" alt="Vietnam" title="Vietnam"></a>
                    <a href="ngon-ngu/en/"><img src="<?= THUMBS ?>/30x20x1/assets/images/en.jpg" alt="English" title="English"></a>
                </div>
                <?php if (array_key_exists($loginMember, $_SESSION) && $_SESSION[$loginMember]['active'] == true) { ?>
                    <div class="user-head">
                        <a href="account/thong-tin">
                            <span>Hi, <?= $_SESSION[$loginMember]['username'] ?></span>
                        </a>
                        <a href="account/dang-xuat">
                            <i class="fas fa-sign-out-alt"></i>
                            <span><?= dangxuat ?></span>
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="user-head">
                        <a href="account/dang-nhap">
                            <i class="fas fa-sign-in-alt"></i>
                            <span><?= dangnhap ?></span>
                        </a>
                        <a href="account/dang-ky">
                            <i class="fas fa-user-plus"></i>
                            <span><?= dangky ?></span>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="head-bottom">
        <div class="wrap-content">
            <a class="logo-head" href="">
                <img onerror="this.src='<?= THUMBS ?>/120x100x2/assets/images/noimage.png';" src="<?= THUMBS ?>/120x100x2/<?= UPLOAD_PHOTO_L . $logo['photo'] ?>" alt="logo" title="logo" />
            </a>
            <a class="banner-head" href="">
                <img onerror="this.src='<?= THUMBS ?>/730x120x2/assets/images/noimage.png';" src="<?= THUMBS ?>/730x120x2/<?= UPLOAD_PHOTO_L . $banner['photo'] ?>" alt="banner" title="banner" />
            </a>
            <p class="hotline-head mb-0">
                <span class="title-hotline d-block">Hotline hỗ trợ:</span>
                <span class="number-hotline"><?= $func->formatPhone($optsetting['hotline']) ?></span>
            </p>
        </div>
    </div>
</div>