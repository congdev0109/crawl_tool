<div class="footer">
    <div class="footer-article">
        <div class="wrap-content padding-top-bottom d-flex flex-wrap justify-content-between">
            <div class="footer-news">
                <p class="name-company"><?= $footer['name' . $lang] ?></p>
                <div class="footer-info"><?= $func->decodeHtmlChars($footer['content' . $lang]) ?></div>
                <ul class="social social-footer list-unstyled d-flex align-items-center ">
                    <?php foreach ($social as $k => $v) { ?>
                        <li class="d-inline-block align-top">
                            <a href="<?= $v['link'] ?>" target="_blank" class="me-2">
                                <img class="lazy" data-src="<?= THUMBS ?>/30x30x2/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>">
                            </a>
                        </li>
                    <?php } ?>
                </ul>   
            </div>
            <div class="footer-news">
                <p class="footer-title"><?= chinhsach ?></p>
                <ul class="footer-ul d-flex flex-wrap justify-content-between">
                    <?php foreach ($policy as $v) { ?>
                        <li><a class=" text-decoration-none " href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="footer-news">
                <p class="footer-title"><?= dangkynhantin ?></p>
                <p class="newsletter-slogan"><?= slogandangkynhantin ?></p>
                <form class="validation-newsletter form-newsletter" novalidate method="post" action="" enctype="multipart/form-data">
                    <div class="newsletter-input">
                        <div class="form-floating form-floating-cus">
                            <input type="email" class="form-control text-sm" id="email-newsletter" name="dataNewsletter[email]" placeholder="<?= nhapemail ?>" required />
                            <label for="email-newsletter">Email</label>
                        </div>
                        <div class="invalid-feedback"><?= vuilongnhapdiachiemail ?></div>
                    </div>
                    <div class="newsletter-button">
                        <input type="hidden" class="" name="dataNewsletter[type]" value="dangkynhantin">
                        <input type="hidden" class="" name="dataNewsletter[date_created]" value="<?= time() ?>">
                        <button class="btn btn-sm btn-danger w-100" type="submit" name="submit-newsletter" value="<?= gui ?>"><?= gui ?></button>
                        <!-- <input type="submit" class="btn btn-sm btn-danger w-100" name="submit-newsletter" value="<?= gui ?>" disabled> -->
                        <input type="hidden" class="btn btn-sm btn-danger w-100" name="recaptcha_response_newsletter" id="recaptchaResponseNewsletter">
                    </div>
                </form>
                <a class="d-inline-block text-decoration-none mt-2" href="https://www.google.com/maps/search/Công+Viên+Phần+Mềm+Quang+Trung+Tô+Ký/@10.8527935,106.6265438,17z" data-fancybox>
                    <i class="bi bi-geo-alt"></i> Google Maps
                </a>
            </div>
            <div class="footer-news">
                <p class="footer-title">Fanpage facebook</p>
                <?= $addons->set('fanpage-facebook', 'fanpage-facebook', 2); ?>
            </div>
        </div>
    </div>
    <div class="footer-tags">
        <div class="wrap-content">
            <p class="footer-title">Tags sản phẩm:</p>
            <ul class="footer-tags-lists w-clear mb-3">
                <?php foreach ($tagsProduct as $v) { ?>
                    <li class="me-1 mb-1"><a class="btn btn-sm btn-danger rounded" href="<?= $v[$sluglang] ?>" target="_blank" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a></li>
                <?php } ?>
            </ul>
            <p class="footer-title">Tags tin tức:</p>
            <ul class="footer-tags-lists w-clear">
                <?php foreach ($tagsNews as $v) { ?>
                    <li class="me-1 mb-1"><a class="btn btn-sm btn-danger rounded" href="<?= $v[$sluglang] ?>" target="_blank" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="footer-powered">
        <div class="wrap-content">
            <div class="row">
                <div class="footer-copyright col-md-6">Copyright © 2023 <?= $copyright['name' . $lang] ?>. </div>
                <div class="footer-statistic col-md-6">
                    <span><?= dangonline ?>: <?= $online ?></span>
                    <span><?= homnay ?>: <?= $counter['today'] ?></span>
                    <span><?= homqua ?>: <?= $counter['yesterday'] ?></span>
                    <span><?= trongtuan ?>: <?= $counter['week'] ?></span>
                    <span><?= trongthang ?>: <?= $counter['month'] ?></span>
                    <span><?= tongtruycap ?>: <?= $counter['total'] ?></span>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="ContactUs">
    <ul class="list-unstyled ">
        <li>
            <a class=" btn-frame text-decoration-none" href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline']); ?>">
                <div class="animated infinite zoomIn kenit-alo-circle"></div>
                <div class="animated infinite pulse kenit-alo-circle-fill"></div>
                <i class="fa-shake" style="--fa-animation-duration: 2s;"><?= $func->getImage(['size-error' => '44x44x1', 'upload' => 'assets/images/', 'image' => 'addthis-phone.png', 'alt' => 'Hotline']) ?></i>
            </a>
        </li>
        <li>
            <a class=" btn-frame text-decoration-none" href="<?= $optsetting['fanpage'] ?>" target="_blank">
                <div class="animated infinite zoomIn kenit-alo-circle"></div>
                <div class="animated infinite pulse kenit-alo-circle-fill"></div>
                <i class="fa-shake" style="--fa-animation-duration: 2s;"><?= $func->getImage(['size-error' => '44x44x1', 'upload' => 'assets/images/', 'image' => 'addthis-mess.png', 'alt' => 'Mess']) ?></i>
            </a>
        </li>
        <li>
            <a class=" btn-frame text-decoration-none" target="_blank" href="https://zalo.me/<?= preg_replace('/[^0-9]/', '', $optsetting['zalo']); ?>">
                <div class="animated infinite zoomIn kenit-alo-circle"></div>
                <div class="animated infinite pulse kenit-alo-circle-fill"></div>
                <i class="fa-shake" style="--fa-animation-duration: 2s;"><?= $func->getImage(['size-error' => '44x44x1', 'upload' => 'assets/images/', 'image' => 'addthis-zalo.png', 'alt' => 'Zalo']) ?></i>
            </a>
        </li>
    </ul>
</div>
<div id="fui-toast"></div>