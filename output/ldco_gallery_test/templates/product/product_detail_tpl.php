<div class="product-detail">
    <div class="wrap-content">
        <div class="row">
            <div class="col-lg-5">
                <div class="product-gallery">
                    <div class="product-main-image">
                        <img id="mainImage" src="<?= THUMBS ?>/500x500x2/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" 
                             alt="<?= $rowDetail['name' . $lang] ?>" />
                    </div>
                    <?php if (!empty($rowDetailPhoto)) { ?>
                    <div class="product-thumbs">
                        <?php foreach ($rowDetailPhoto as $img) { ?>
                            <img class="thumb-item" 
                                 src="<?= THUMBS ?>/100x100x1/<?= UPLOAD_GALLERY_L . $img['photo'] ?>" 
                                 data-full="<?= THUMBS ?>/500x500x2/<?= UPLOAD_GALLERY_L . $img['photo'] ?>"
                                 alt="<?= $rowDetail['name' . $lang] ?>" />
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="product-info">
                    <h1 class="product-title"><?= $rowDetail['name' . $lang] ?></h1>
                    <div class="product-price">
                        <?php if ($rowDetail['sale_price'] && $rowDetail['sale_price'] < $rowDetail['regular_price']) { ?>
                            <span class="price-new"><?= $func->formatMoney($rowDetail['sale_price']) ?></span>
                            <span class="price-old"><?= $func->formatMoney($rowDetail['regular_price']) ?></span>
                        <?php } else { ?>
                            <span class="price-new"><?= ($rowDetail['regular_price']) ? $func->formatMoney($rowDetail['regular_price']) : lienhe ?></span>
                        <?php } ?>
                    </div>
                    <div class="product-desc">
                        <?= $rowDetail['desc' . $lang] ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="product-content mt-4">
            <h2>Mô tả chi tiết</h2>
            <div class="content-body">
                <?= $rowDetail['content' . $lang] ?>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($product)) { ?>
        <div class="related-products mt-4">
            <h2>Sản phẩm liên quan</h2>
            <div class="row">
                <?php foreach ($product as $v) { ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <div class="box-product">
                            <a class="pic-product scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['name' . $lang] ?>">
                                <img class="lazy w-100" 
                                     onerror="this.src='<?= THUMBS ?>/285x285x1/assets/images/noimage.png';" 
                                     data-src="<?= THUMBS ?>/285x285x2/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" 
                                     alt="<?= $v['name' . $lang] ?>" />
                            </a>
                            <h3 class="name-product"><a href="<?= $v['slugvi'] ?>"><?= $v['name' . $lang] ?></a></h3>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>