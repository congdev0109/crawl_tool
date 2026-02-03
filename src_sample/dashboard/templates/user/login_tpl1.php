<div class="login-view-website text-sm"><a href="<?= $configBase ?>" target="_blank" title="Xem website"><i
            class="fas fa-reply mr-2"></i>Xem website</a></div>

<div class="wrap-login-page">
    <div class="login-box">
        <div class="card">
            <div class="login_l col-sm-5 col-md-5">
                <div class="card-body login-card-body">
                    <div class="skd-logo">
                        <a href="#"><img src="assets/images/logo_admin.png" width="250px"></a>
                    </div>
                    <form enctype="multipart/form-data" id="form-login">
                        <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="recaptcha_response" id="recaptcha_response">
                        
                        <div class="input-group mb-3">
                            <div class="input-group-append login-input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <input type="text" class="form-control text-sm" name="username" id="username" 
                                placeholder="Tài khoản *" autocomplete="off">
                        </div>
                        
                        <div class="input-group mb-3">
                            <div class="input-group-append login-input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                            <input type="password" class="form-control text-sm" name="password" id="password"
                                placeholder="Mật khẩu *" autocomplete="off">
                            <div class="input-group-append">
                                <div class="input-group-text show-password">
                                    <span class="fas fa-eye"></span>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm bg-gradient-danger btn-block btn-login text-sm p-2 auth-form-btn">
                            <div class="sk-chase d-none">
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                            </div>
                            <span>ĐĂNG NHẬP</span>
                        </button>

                        <div class="alert my-alert alert-login d-none text-center text-sm p-2 mb-0 mt-2" role="alert"></div>
                    </form>
                </div>
            </div>
            <div class="login-info-company">
                <div class="login-info-content">
                    <div class="tks">Cảm ơn bạn đã tin tưởng và lựa chọn DTS VIỆT NAM!</div>

                    <p>Chúng tôi sẽ nỗ lực hết mình để mang đến những trải nghiệm tốt nhất và giúp việc kinh doanh của bạn
                        thành công. </p>

                    <p><b>CSKH:</b> info@vndts.vn </p>

                    <p><b>HOTLINE:</b> 02862.70.72.73 </p>

                    <p><b>Địa chỉ:</b> Toà Nhà D-Eyes, 371 Nguyễn Kiệm, P.3, Gò Vấp, TP.HCM </p>
                </div>
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3" data-top="70" style="transition: 2s ease;"></div>
                <div class="circle circle-4" data-right="70" style="transition: 3s ease;"></div>
            </div>
        </div>
    </div>
</div>
<div class="login-copyright text-sm">Powered by <a href="#" target="_blank"
        title="Viet Nam Digital Technology Solution">Viet Nam Digital Technology Solution</a></div>
