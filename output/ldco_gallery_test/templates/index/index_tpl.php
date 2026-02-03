<!-- Featured Products -->
<?php if (!empty($featuredProducts)) { ?>
<div class="section-product padding-top-bottom">
    <div class="wrap-content">
        <div class="title-main"><span>Sản phẩm nổi bật</span></div>
        <div class="row">
            <?php foreach ($featuredProducts as $v) { ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="box-product">
                        <a class="pic-product scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['namevi'] ?>">
                            <img class="lazy w-100" 
                                 onerror="this.src='<?= THUMBS ?>/285x285x1/assets/images/noimage.png';" 
                                 data-src="<?= THUMBS ?>/285x285x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" 
                                 alt="<?= $v['namevi'] ?>" />
                        </a>
                        <h3 class="name-product"><a href="<?= $v['slugvi'] ?>"><?= $v['namevi'] ?></a></h3>
                        <p class="price-product">
                            <?php if ($v['sale_price'] && $v['sale_price'] < $v['regular_price']) { ?>
                                <span class="price-new"><?= $func->formatMoney($v['sale_price']) ?></span>
                                <span class="price-old"><?= $func->formatMoney($v['regular_price']) ?></span>
                            <?php } else { ?>
                                <span class="price-new"><?= ($v['regular_price']) ? $func->formatMoney($v['regular_price']) : lienhe ?></span>
                            <?php } ?>
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<!-- Latest News -->
<?php if (!empty($latestNews)) { ?>
<div class="section-news padding-top-bottom">
    <div class="wrap-content">
        <div class="title-main"><span>Tin tức mới nhất</span></div>
        <div class="row">
            <?php foreach ($latestNews as $v) { ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="box-news">
                        <a class="pic-news scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['namevi'] ?>">
                            <img class="lazy w-100" 
                                 onerror="this.src='<?= THUMBS ?>/420x288x1/assets/images/noimage.png';" 
                                 data-src="<?= THUMBS ?>/420x288x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" 
                                 alt="<?= $v['namevi'] ?>" />
                        </a>
                        <h3 class="name-news"><a href="<?= $v['slugvi'] ?>"><?= $v['namevi'] ?></a></h3>
                        <p class="date-news"><?= date("d/m/Y", $v['date_created']) ?></p>
                        <p class="desc-news text-split"><?= $v['descvi'] ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>