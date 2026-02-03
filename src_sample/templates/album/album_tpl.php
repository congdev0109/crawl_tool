<div class="title-main"><span><?=$titleMain?></span></div>
<?php if(isset($product) && count($product) > 0) { ?> 
        <div class="row flex-cus">
            <?php foreach($product as $k=>$v) { ?>
                <div class="pb-4 col-6 col-md-3 col-sm-4 mg-cus">
                    <div class="box-product" data-aos="fade-up" data-aos-duration="1000">
                        <p class="pic-product">
                            <a class="text-decoration-none scale-img" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>">
                                <img class="lazy w-100" onerror="this.src='<?=THUMBS?>/285x285x1/assets/images/noimage.png';" data-src="<?=THUMBS?>/285x285x2/<?=UPLOAD_PRODUCT_L.$v['photo']?>" alt="<?=$v['name'.$lang]?>" title="<?=$v['name'.$lang]?>"/>
                            </a>
                        </p>
                        <h3>
                            <a class="text-decoration-none name-product text-split" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>"><?=$v['name'.$lang]?></a>
                        </h3>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="pagination-home w-100"><?=(!empty($paging)) ? $paging : ''?></div>
<?php } else { ?>
    <div class="alert alert-warning" role="alert">
        <strong><?=khongtimthayketqua?></strong>
    </div>
<?php } ?>