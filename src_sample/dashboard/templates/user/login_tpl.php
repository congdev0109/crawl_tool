<link rel="stylesheet" href="assets/css/login.css">

<div class="container-fluid page-body-wrapper w-100 full-page-wrapper">
    <div class="content-wrapper auth m-0">
        <div class="row">
            <div class="col-lg-2 hidden-md">

                <div id="icon-user">
                    <div id="confetti-animation">
                    </div>
                </div>
                <div id="confetti-animation1">

                </div>

            </div>
            <div class="col-lg-10 bg-linear-gradient col-md-12" style="background: url(assets/images/bg_auth.png) no-repeat center;background-size: cover;">
                <div class="auth-form-light text-left">
                    <div class="brand-logo">
                        <a href="//vndts.vn" target="_blank">
                            <img src="./assets/images/logo.png" alt="">
                        </a>
                    </div>
                    <form enctype="multipart/form-data" id="form-login">
                        <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="recaptcha_response" id="recaptcha_response">
                        <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container">
                            <input type="text" class="form-control text-sm" name="username" id="username"
                                placeholder="Tài khoản *" autocomplete="off">
                            <label for="email">Username</label>
                            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                        </div>
                        <div class="mb-5 fv-plugins-icon-container">
                            <div class="form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" class="form-control" name="password" id="password"
                                            placeholder="Mật khẩu">
                                        <label for="password">Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer show-password"> <span class="fas fa-eye"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                        </div>

                        <div class="mt-3">
                            <button type="button"
                                class="btn btn-block btn-success btn-lg font-weight-medium auth-form-btn btn-login  w-100 mb-3">
                                <div class="sk-chase d-none">
                                    <div class="sk-chase-dot"></div>
                                    <div class="sk-chase-dot"></div>
                                    <div class="sk-chase-dot"></div>
                                    <div class="sk-chase-dot"></div>
                                    <div class="sk-chase-dot"></div>
                                    <div class="sk-chase-dot"></div>
                                </div>
                                <span>Đăng nhập</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15"
                                    fill="none">
                                    <path
                                        d="M6.74411 1.44813C4.36047 1.5648 2.61731 2.60513 1.47766 4.6717C1.43219 4.75417 1.39196 4.83966 1.34714 4.92281C1.15382 5.28154 0.739376 5.41833 0.391987 5.23829C0.0459058 5.05926 -0.0993304 4.6318 0.0720745 4.26569C0.71517 2.89211 1.68178 1.81927 2.9575 1.04213C4.14785 0.31695 5.44222 -0.035413 6.8249 0.00280708C9.07835 0.0655015 10.9311 1.00022 12.3691 2.77678C13.2948 3.92037 13.8414 5.24433 13.9657 6.72351C14.2015 9.52867 13.2209 11.8122 11.0495 13.5351C9.94127 14.4145 8.66718 14.9113 7.26486 14.9861C4.13902 15.1527 1.79332 13.8204 0.216002 11.0558C0.161048 10.9596 0.116888 10.857 0.0714203 10.7557C-0.0944237 10.3846 0.0455787 9.96183 0.391987 9.78347C0.741012 9.60343 1.16494 9.73418 1.34518 10.1006C1.74 10.9049 2.25323 11.612 2.93918 12.1786C4.18416 13.2075 5.60675 13.6695 7.19977 13.521C9.02929 13.3507 10.4679 12.4652 11.5313 10.9391C12.8692 9.01873 12.9225 6.27996 11.6749 4.3019C10.7731 2.872 9.51505 1.96209 7.89291 1.59933C7.4909 1.50948 7.07383 1.49004 6.74411 1.44813Z"
                                        fill="white" />
                                    <path
                                        d="M8.11731 8.24628C8.02834 8.24628 7.97109 8.24628 7.91418 8.24628C6.63845 8.24628 5.3624 8.24796 4.08668 8.24528C3.57475 8.24427 3.23096 7.77389 3.37751 7.28541C3.47106 6.97395 3.74877 6.77682 4.10761 6.77648C5.37254 6.77514 6.63747 6.77581 7.9024 6.77581C7.96161 6.77581 8.02049 6.77581 8.12189 6.77581C7.85562 6.50157 7.61651 6.25448 7.37674 6.00772C7.28057 5.90882 7.18178 5.8126 7.08659 5.71269C6.7935 5.40492 6.7886 4.95466 7.07351 4.6603C7.34795 4.377 7.80067 4.37096 8.08918 4.66298C8.69825 5.27919 9.30733 5.89574 9.903 6.52571C10.4375 7.09096 10.4241 7.96231 9.88566 8.52086C9.29556 9.13305 8.70153 9.74089 8.10619 10.3474C7.81081 10.6481 7.35613 10.6535 7.07678 10.3678C6.78598 10.0701 6.79252 9.61516 7.09444 9.30337C7.40552 8.98219 7.71889 8.66335 8.03095 8.34317C8.05254 8.32105 8.07217 8.29691 8.11731 8.24628Z"
                                        fill="white" />
                                </svg>
                            </button>
                        </div>
                        <div class="alert my-alert alert-login d-none mb-0 mt-2 alert alert-danger" role="alert"></div>
                        <a class="mt-3 text-center d-flex justify-content-center align-items-center" href="<?= $configBase ?>" target="_blank" title="Xem website"><i
                                class="fas fa-reply mr-2"></i>Xem website</a>
                    </form>

                    <div class="text-center mt-4 text-copyright">
                        <svg xmlns="http://www.w3.org/2000/svg" width="182" height="2" viewBox="0 0 182 2" fill="none">
                            <path d="M181 1H1" stroke="#CDCDCD" stroke-miterlimit="10" stroke-linecap="round" />
                        </svg>
                        <br>
                        <span class="text-copyright-text mt-5 d-block">© CÔNG TY TNHH GIẢI PHÁP CÔNG NGHỆ SỐ VN. <br>
                            Design by VNDTS</span>
                    </div>
                    <div class="container">
                        <div class="grid-container">
                            <div class="grid">
                                <div class="grid-lines"></div>
                            </div>
                            <div class="gradient-overlay"></div>
                        </div>
                    </div>
                </div>
                <div class="auth_login_info">
                    <div class="auth_login_info_item">
                        <span class="span__wellcome">Welcome to</span>
                        <span class="span__wellcome_text mb-4">CÔNG TY TNHH <br>
                            GIẢI PHÁP CÔNG NGHỆ SỐ việt nam</span>
                        <p>THANK YOU <br>
                            FOR YOUR TRUST AND CHOICE! 👋</p>
                        <small>Chúng tôi sẽ nỗ lực hết mình để mang đến những trải nghiệm tốt nhất <br> & giúp việc kinh
                            doanh
                            của bạn thành công.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>