<?php

/**
 * login
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
                        <h3 class="block-title"><?= lang('Auth.login') ?></h3>
                        <div class="block-options">
                            <?php if (setting('Auth.allowRegistration')) : ?>
                                <a class="btn-block-option" href="<?= url_to('register') ?>" data-bs-toggle="tooltip" data-bs-placement="left" title="<?= lang('Auth.needAccount') ?>">
                                    <i class="fa fa-user-plus"></i>
                                </a>
                            <?php endif; ?>
                        </div>
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
                                Welcome, please login.
                            </p>
                            <?= $this->include('auth/sections/alerts'); ?>
                            <!-- Sign In Form -->
                            <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js) -->
                            <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                            <form class="js-validation-signin" action="<?php echo base_url('login'); ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="py-3">
                                    <div class="mb-4">
                                        <input type="text" class="form-control form-control-alt form-control-lg" id="login-email" name="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <input type="password" class="form-control form-control-alt form-control-lg" id="login-password" name="password" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>" required>
                                    </div>
                                    <?php if (setting('Auth.sessionConfig')['allowRemembering']) : ?>
                                        <div class="mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" <?php if (old('remember')) : ?> checked<?php endif ?> id="login-remember" name="remember">
                                                <label class="form-check-label" for="remember"><?= lang('Auth.rememberMe') ?></label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6 col-xl-5">
                                        <button type="submit" class="btn w-100 btn-alt-primary">
                                            <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i>
                                            <?= lang('Auth.login') ?>
                                        </button>
                                    </div>
                                </div>
                                <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                                    <p class="text-center"><?= lang('Auth.forgotPassword') ?> <a href="<?= url_to('magic-link') ?>"><?= lang('Auth.useMagicLink') ?></a></p>
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