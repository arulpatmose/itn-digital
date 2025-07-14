<?php

/**
 * magic-link-message
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
                <div class="container d-flex justify-content-center p-5">
                    <div class="card card-borderless push">
                        <div class="card-header">
                            <h3 class="block-title">
                                <?= lang('Auth.useMagicLink') ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <?= lang('Auth.checkYourEmail') ?>
                        </div>
                        <div class="card-footer fs-sm border-top-0">
                            <?= lang('Auth.magicLinkDetails', [setting('Auth.magicLinkLifetime') / 60]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fs-sm text-center text-white">
            <strong><?php echo config('Template')->site_title; ?></strong> &copy; <span data-toggle="year-copy"></span>
        </div>
    </div>
</div>
<!-- END Page Content -->

<?= $this->endSection() ?>