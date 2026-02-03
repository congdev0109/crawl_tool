<div class="section-news">
    <div class="wrap-content">
        <h1 class="title-page"><?= $seo->get('h1') ?></h1>
        
        <?php if (!empty($news)) { ?>
        <div class="row">
            <?php foreach ($news as $v) { ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="box-news">
                        <a class="pic-news scale-img" href="<?= $v['slugvi'] ?>" title="<?= $v['name' . $lang] ?>">
                            <img class="lazy w-100" 
                                 onerror="this.src='<?= THUMBS ?>/420x288x1/assets/images/noimage.png';" 
                                 data-src="<?= THUMBS ?>/420x288x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" 
                                 alt="<?= $v['name' . $lang] ?>" />
                        </a>
                        <div class="info-news">
                            <h3 class="name-news"><a href="<?= $v['slugvi'] ?>"><?= $v['name' . $lang] ?></a></h3>
                            <p class="date-news"><?= date("d/m/Y", $v['date_created']) ?></p>
                            <p class="desc-news text-split"><?= $v['desc' . $lang] ?></p>
                        </div>
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