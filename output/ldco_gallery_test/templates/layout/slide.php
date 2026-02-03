<?php if (!empty($slide)) { ?>
<div class="slideshow">
    <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:1" data-rewind="1" data-autoplay="1" data-loop="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplaytimeout="5000" data-dots="0" data-nav="1" data-navcontainer=".control-slideshow">
        <?php foreach ($slide as $v) { ?>
            <div class="slideshow-item">
                <a class="slideshow-image" href="<?= $v['link'] ?>" title="<?= $v['namevi'] ?>">
                    <picture>
                        <source media="(max-width: 500px)" srcset="<?= THUMBS ?>/500x200x1/<?= UPLOAD_PHOTO_L . $v['photo'] ?>">
                        <img class="lazy w-100" 
                             onerror="this.src='<?= THUMBS ?>/1920x770x1/assets/images/noimage.png';" 
                             data-src="<?= THUMBS ?>/1920x770x1/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" 
                             alt="<?= $v['namevi'] ?>" title="<?= $v['namevi'] ?>" />
                    </picture>
                </a>
            </div>
        <?php } ?>
    </div>
    <div class="control-slideshow control-owl transition"></div>
</div>
<?php } ?>