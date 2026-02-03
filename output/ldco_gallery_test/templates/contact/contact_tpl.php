<div class="contact-page">
    <div class="wrap-content">
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-info">
                    <h1 class="contact-title">Liên hệ với chúng tôi</h1>
                    <p><i class="bi bi-building"></i> <strong><?= $optsetting['ten-cong-ty'] ?></strong></p>
                    <p><i class="bi bi-geo-alt"></i> <?= $optsetting['diachi'] ?></p>
                    <p><i class="bi bi-telephone"></i> <?= $optsetting['hotline'] ?></p>
                    <p><i class="bi bi-envelope"></i> <?= $optsetting['email'] ?></p>
                </div>
                <div class="contact-map mt-4">
                    <?php if (!empty($optsetting['map'])) { ?>
                        <?= $optsetting['map'] ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-form">
                    <h2>Gửi tin nhắn</h2>
                    <?php if ($flash->has('success')) { ?>
                        <div class="alert alert-success"><?= $flash->get('success') ?></div>
                    <?php } ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Họ tên *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội dung</label>
                            <textarea name="content" class="form-control" rows="5"></textarea>
                        </div>
                        <button type="submit" name="contact_submit" class="btn btn-primary">Gửi liên hệ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>