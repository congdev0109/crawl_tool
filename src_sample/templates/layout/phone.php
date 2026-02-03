
<?php if ($com != 'gio-hang') { ?>
    <div>
        <a class="cart-fixed text-decoration-none" href="gio-hang" title="Giỏ hàng">
            <i class="bi bi-cart3"></i>
            <span class="count-cart"><?= (!empty($_SESSION['cart'])) ? count($_SESSION['cart']) : 0 ?></span>
        </a>
    </div>
<?php } ?>
<a class="btn-zalo btn-frame text-decoration-none" target="_blank" href="https://zalo.me/<?= preg_replace('/[^0-9]/', '', $optsetting['zalo']); ?>">
    <div class="animated infinite zoomIn kenit-alo-circle"></div>
    <div class="animated infinite pulse kenit-alo-circle-fill"></div>
    <i><?= $func->getImage(['size-error' => '35x35x2', 'upload' => 'assets/images/', 'image' => 'zl.png', 'alt' => 'Zalo']) ?></i>
</a>
<a class="btn-phone btn-frame text-decoration-none" href="tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline']); ?>">
    <div class="animated infinite zoomIn kenit-alo-circle"></div>
    <div class="animated infinite pulse kenit-alo-circle-fill"></div>
    <i><?= $func->getImage(['size-error' => '35x35x2', 'upload' => 'assets/images/', 'image' => 'hl.png', 'alt' => 'Hotline']) ?></i>
</a>
<a href="javascript:void();" class="btn-datlich" data-bs-toggle="modal" data-bs-target=".booking"><i class="far fa-calendar-alt"></i><br>Đặt lịch</a>

<?= $addons->set('footer-map', 'footer-map', 6); ?>
<?= $addons->set('messages-facebook', 'messages-facebook', 2);?>