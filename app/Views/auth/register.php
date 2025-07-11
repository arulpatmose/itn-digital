<?php

/**
 * register
 *
 * Author: Arul Patmose
 *
 * Load Default Global Template and Extend
 *
 */
?>
<?= $this->extend('auth') ?>

<?= $this->section('content') ?>

<div class="hero-static d-flex align-items-center">
    <div class="content">
        <div class="row justify-content-center push">
            <div class="col-md-8 col-lg-6 col-xl-4">
                <!-- Sign In Block -->
                <div class="block block-rounded mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><?= lang('Auth.register') ?></h3>
                    </div>
                    <div class="block-content">
                        <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">
                            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                                <img class="site-logo dark-logo mb-2"
                                    src="<?php echo base_url('assets/media/logos/dark_logo.png'); ?>"
                                    alt="ITN Digital">
                                <img class="site-logo light-logo mb-2"
                                    src="<?php echo base_url('assets/media/logos/light_logo.png'); ?>"
                                    alt="ITN Digital">
                            </a>
                            <p class="fw-medium text-muted">
                                Welcome, please register.
                            </p>
                            <?= $this->include('auth/sections/alerts'); ?>
                            <!-- Sign In Form -->
                            <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js) -->
                            <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                            <form class="js-validation-signin" action="<?= url_to('register') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="py-3">
                                    <div class="mb-4">
                                        <input type="text" class="form-control form-control-alt form-control-lg"
                                            id="login-first-name" name="first_name" inputmode="text"
                                            placeholder="First Name" value="<?= old('first_name') ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <input type="text" class="form-control form-control-alt form-control-lg"
                                            id="login-last-name" name="last_name" inputmode="text"
                                            placeholder="Last Name" value="<?= old('last_name') ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <input type="text" class="form-control form-control-alt form-control-lg"
                                            id="login-email" name="email" inputmode="email"
                                            placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>"
                                            required>
                                    </div>
                                    <div class="mb-4">
                                        <input type="text" class="form-control form-control-alt form-control-lg"
                                            id="login-username" name="username" inputmode="text"
                                            placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>"
                                            required>
                                    </div>
                                    <div class="mb-4">
                                        <input type="password" class="form-control form-control-alt form-control-lg"
                                            id="login-password" name="password" inputmode="text"
                                            placeholder="<?= lang('Auth.password') ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <input type="password" class="form-control form-control-alt form-control-lg"
                                            id="login-confirm-password" name="password_confirm" inputmode="text"
                                            placeholder="<?= lang('Auth.passwordConfirm') ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6 col-xl-5">
                                        <button type="submit" class="btn w-100 btn-alt-primary">
                                            <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i>
                                            <?= lang('Auth.register') ?>
                                        </button>
                                    </div>
                                </div>
                                <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                                    <p class="text-center"><?= lang('Auth.haveAccount') ?> <a
                                            href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a></p>
                                <?php endif ?>
                            </form>
                            <!-- END Sign In Form -->
                        </div>
                    </div>
                </div>
                <!-- END Sign In Block -->
            </div>
        </div>
        <div class="fs-sm text-muted text-center">
            <strong><?php echo config('Template')->site_title; ?></strong> &copy; <span data-toggle="year-copy"></span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>