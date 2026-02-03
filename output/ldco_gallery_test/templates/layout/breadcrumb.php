<?php if (!empty($breadcrumbs) && count($breadcrumbs) > 0) { ?>
<div class="breadcrumb-wrap">
    <div class="wrap-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Trang chủ</a></li>
                <?php foreach ($breadcrumbs as $slug => $name) { ?>
                    <li class="breadcrumb-item"><a href="<?= $slug ?>"><?= $name ?></a></li>
                <?php } ?>
            </ol>
        </nav>
    </div>
</div>
<?php } ?>