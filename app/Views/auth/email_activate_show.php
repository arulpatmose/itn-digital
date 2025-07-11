<?php

/**
 * email-activate-show
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
                        <h3 class="block-title"><?= lang('Auth.emailActivateTitle') ?></h3>
                        <div class="block-options">

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
                                <?= lang('Auth.emailActivateBody') ?>
                            </p>
                            <?= $this->include('auth/sections/alerts'); ?>
                            <!-- Sign In Form -->
                            <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js) -->
                            <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                            <form class="js-validation-signin" action="<?php echo base_url('auth/a/verify'); ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="py-3">
                                    <div class="mb-4">
                                        <input type="text" class="form-control form-control-alt form-control-lg" id="login-email" name="token" placeholder="000000" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" value="<?= old('token') ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6 col-xl-5">
                                        <button type="submit" class="btn w-100 btn-alt-primary">
                                            <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i>
                                            <?= lang('Auth.send') ?>
                                        </button>
                                    </div>
                                </div>
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