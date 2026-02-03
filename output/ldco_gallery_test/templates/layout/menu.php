<nav class="main-menu">
    <div class="wrap-content">
        <div class="menu-toggle d-lg-none">
            <i class="bi bi-list"></i>
        </div>
        <ul class="menu-list d-flex flex-wrap align-items-center">
            <li><a href="" title="Trang chủ">Trang chủ</a></li>
            <?php
            $menuMain = $cache->get("select id, namevi, slugvi from #_menu where type = 'main' and find_in_set('hienthi',status) order by numb,id desc", null, 'query', 3600);
            foreach ($menuMain as $menu) {
            ?>
                <li><a href="<?= $menu['slugvi'] ?>" title="<?= $menu['namevi'] ?>"><?= $menu['namevi'] ?></a></li>
            <?php } ?>
        </ul>
    </div>
</nav>