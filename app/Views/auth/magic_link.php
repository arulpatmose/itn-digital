<?php

/**
 * magic-link
 *
 * Author: Arul Patmose
 *
 * Load Default Global Template and Extend
 *
 */
?>
<?= $this->extend('auth') ?>

<?= $this->section('content') ?>
<!-- Page Content -->
<div class="hero-static d-flex align-items-center">
    <div class="content">
        <div class="row justify-content-center push">
            <div class="col-md-8 col-lg-6 col-xl-4">
                <!-- Unlock Block -->
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><?= lang('Auth.useMagicLink') ?></h3>
                        <div class="block-options">
                            <a class="btn-block-option" href="<?= base_url('login') ?>" data-bs-toggle="tooltip" data-bs-placement="left" title="Sign In with another account">
                                <i class="fa fa-sign-in-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5 text-center">
                            <?= $this->include('auth/sections/alerts'); ?>
                            <!-- Unlock Form -->
                            <!-- jQuery Validation (.js-validation-lock class is initialized in js/pages/op_auth_lock.min.js which was auto compiled from _js/pages/op_auth_lock.js) -->
                            <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                            <form class="js-validation-lock mt-1" action="<?= url_to('magic-link') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="mb-4">
                                    <input type="email" class="form-control form-control-lg form-control-alt" id="email" name="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email', auth()->user()->email ?? null) ?>">
                                </div>
                                <div class="row justify-content-center mb-4">
                                    <div class="col-md-6 col-xl-5">
                                        <button type="submit" class="btn w-100 btn-alt-success">
                                            <i class="fa fa-fw fa-lock-open me-1 opacity-50"></i>
                                            <?= lang('Auth.send') ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- END Unlock Form -->
                        </div>
                    </div>
                </div>
                <!-- END Unlock Block -->
            </div>
        </div>
        <div class="fs-sm text-center text-white">
            <strong><?php echo config('Template')->site_title; ?></strong> &copy; <span data-toggle="year-copy"></span>
        </div>
    </div>
</div>
<!-- END Page Content -->

<?= $this->endSection() ?>