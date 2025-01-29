<?php

/**
 * dashboard
 *
 * Author: Arul Patmose
 *
 * Load Default Global Template and Extend
 *
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>

<div class="row push">
    <div class="col-3 col-md-3 col-xxl-2">
        <a class="block block-rounded block-link-pop text-center" href="<?php echo site_url('daily-schedule'); ?>">
            <div class="block-content block-content-full ratio ratio-1x1">
                <div class="d-flex justify-content-center align-items-center">
                    <div>
                        <i class="fa fa-2x fa-table-list text-modern"></i>
                        <div class="fs-sm fw-semibold mt-3 text-uppercase">Ad Schedule</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>