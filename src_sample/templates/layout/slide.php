<?php if (count($slider)) { ?>
    <div class="slideshow">
        <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:1" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="0" data-touchdrag="0" data-smartspeed="800" data-autoplayspeed="800" data-autoplaytimeout="5000" data-dots="0" data-animations="animate__fadeInDown, animate__backInUp, animate__rollIn, animate__backInRight, animate__zoomInUp, animate__backInLeft, animate__rotateInDownLeft, animate__backInDown, animate__zoomInDown, animate__fadeInUp, animate__zoomIn" data-nav="1" data-navcontainer=".control-slideshow">
            <?php foreach ($slider as $v) { ?>
                <div class="slideshow-item" owl-item-animation>
                    <a class="slideshow-image" href="<?= $v['link'] ?>" target="_blank" title="<?= $v['name' . $lang] ?>">
                        <picture>
                            <source media="(max-width: 500px)" srcset="<?= THUMBS ?>/500x200x1/<?= UPLOAD_PHOTO_L . $v['photo'] ?>">
                            <img class="lazy w-100" onerror="this.src='<?= THUMBS ?>/1366x600x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/1366x600x1/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                        </picture>
                    </a>
                </div>
            <?php } ?>
        </div>
        <div class="control-slideshow control-owl transition"></div>
    </div>
<?php } ?>