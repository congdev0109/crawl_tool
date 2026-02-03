<div class="news-detail">
    <div class="wrap-content">
        <div class="row">
            <div class="col-lg-8">
                <article class="news-article">
                    <h1 class="news-title"><?= $rowDetail['name' . $lang] ?></h1>
                    <p class="news-date"><i class="bi bi-calendar"></i> <?= date("d/m/Y", $rowDetail['date_created']) ?></p>
                    <div class="news-desc">
                        <?= $rowDetail['desc' . $lang] ?>
                    </div>
                    <div class="news-content">
                        <?= $rowDetail['content' . $lang] ?>
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <div class="sidebar">
                    <h3>Bài viết liên quan</h3>
                    <?php if (!empty($news)) { ?>
                        <?php foreach ($news as $v) { ?>
                            <div class="sidebar-item d-flex mb-3">
                                <a class="sidebar-img" href="<?= $v['slugvi'] ?>">
                                    <img src="<?= THUMBS ?>/100x68x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>">
                                </a>
                                <div class="sidebar-info">
                                    <a href="<?= $v['slugvi'] ?>"><?= $v['name' . $lang] ?></a>
                                    <p><?= date("d/m/Y", $v['date_created']) ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>