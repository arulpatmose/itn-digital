<?php

/**
 * set-password
 *
 * Author: Arul Patmose
 *
 * Shown after a successful magic-link login so the user can set a password.
 *
 */
?>
<?= $this->extend('auth') ?>

<?= $this->section('content') ?>

<div class="hero-static d-flex align-items-center">
    <div class="content">
        <div class="row justify-content-center push">
            <div class="col-md-8 col-lg-6 col-xl-4">
                <!-- Set Password Block -->
                <div class="block block-rounded mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Set a New Password</h3>
                        <div class="block-options">
                            <a class="btn-block-option" href="<?= url_to('logout') ?>" data-bs-toggle="tooltip" data-bs-placement="left" title="Sign out">
                                <i class="fa fa-sign-out-alt"></i>
                            </a>
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
                                You've signed in with a magic link. Please set a password to secure your account.
                            </p>
                            <?= $this->include('auth/sections/alerts'); ?>
                            <!-- Set Password Form -->
                            <form class="js-validation-signin" action="<?= url_to('set-password') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="py-3">
                                    <div class="mb-4">
                                        <input type="password" class="form-control form-control-alt form-control-lg" id="password" name="password" autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <input type="password" class="form-control form-control-alt form-control-lg" id="password_confirm" name="password_confirm" autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" required>
                                    </div>
                                </div>
                                <div class="row mb-0">
                                    <div class="col-md-6 col-xl-7">
                                        <button type="submit" class="btn w-100 btn-alt-primary">
                                            <i class="fa fa-fw fa-key me-1 opacity-50"></i>
                                            Set Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- END Set Password Form -->
                        </div>
                    </div>
                </div>
                <!-- END Set Password Block -->
            </div>
        </div>
        <div class="fs-sm text-muted text-center">
            <strong><?php echo config('Template')->site_title; ?></strong> &copy; <span data-toggle="year-copy"></span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
