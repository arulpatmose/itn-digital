<?php

/**
 * index
 *
 * Author: Arul Patmose
 *
 * Load Default Global Template and Extend
 *
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-6 col-md-4 col-xxl-3">
            <a class="block block-rounded block-link-pop text-center" href="<?= site_url('settings/system') ?>">
                <div class="block-content block-content-full ratio ratio-16x9">
                    <div class="d-flex justify-content-center align-items-center">
                        <div>
                            <i class="fa fa-2x fa-cogs text-modern"></i>
                            <div class="fs-sm fw-semibold mt-3 text-uppercase">System Settings</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>

<?= $this->endSection() ?>

<?= $this->section('other-styles') ?>

<?= $this->endSection() ?>