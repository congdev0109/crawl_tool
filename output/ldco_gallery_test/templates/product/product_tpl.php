<div class="section-product">
    <div class="wrap-content">
        <h1 class="title-page"><?= $seo->get('h1') ?></h1>
        
        <?php if (!empty($product)) { ?>
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
        
        <?php if (!empty($paging)) { ?>
            <div class="paging"><?= $paging ?></div>
        <?php } ?>
        <?php } else { ?>
            <p class="no-data">Không có dữ liệu</p>
        <?php } ?>
    </div>
</div>