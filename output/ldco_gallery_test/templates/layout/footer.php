<?php if (!empty($doitac)) { ?>
<div class="wrap-partner padding-top-bottom">
    <div class="wrap-content">
        <div class="title-main"><span>Đối tác</span></div>
        <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:575|items:3|margin:10,screen:991|items:5|margin:10" data-rewind="1" data-autoplay="1" data-loop="0" data-dots="0" data-nav="1" data-navcontainer=".control-partner">
            <?php foreach ($doitac as $v) { ?>
                <div>
                    <a class="partner" href="<?= $v['link'] ?>" target="_blank" title="<?= $v['namevi'] ?>">
                        <img class="w-100 lazy" 
                             onerror="this.src='<?= THUMBS ?>/204x55x2/assets/images/noimage.png';" 
                             data-src="<?= THUMBS ?>/204x55x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" 
                             alt="<?= $v['namevi'] ?>" />
                    </a>
                </div>
            <?php } ?>
        </div>
        <div class="control-partner control-owl transition"></div>
    </div>
</div>
<?php } ?>

<footer class="footer">
    <div class="wrap-content">
        <div class="row">
            <div class="col-lg-4">
                <h4 class="footer-title">Thông tin liên hệ</h4>
                <p><i class="bi bi-geo-alt"></i> <?= $optsetting['diachi'] ?></p>
                <p><i class="bi bi-telephone"></i> <?= $optsetting['hotline'] ?></p>
                <p><i class="bi bi-envelope"></i> <?= $optsetting['email'] ?></p>
            </div>
            <div class="col-lg-4">
                <h4 class="footer-title">Liên kết</h4>
                <?php
                $menuFooter = $cache->get("select id, namevi, slugvi from #_menu where type = 'footer' and find_in_set('hienthi',status) order by numb,id desc", null, 'query', 3600);
                foreach ($menuFooter as $menu) {
                ?>
                    <p><a href="<?= $menu['slugvi'] ?>"><?= $menu['namevi'] ?></a></p>
                <?php } ?>
            </div>
            <div class="col-lg-4">
                <h4 class="footer-title">Fanpage</h4>
                <div class="footer-fanpage">
                    <!-- Facebook Page Plugin -->
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <p>&copy; <?= date('Y') ?> <?= $optsetting['ten-cong-ty'] ?>. All rights reserved.</p>
        </div>
    </div>
</footer>