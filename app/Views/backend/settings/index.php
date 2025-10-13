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
        <?php foreach ($categories as $cat => $info): ?>
            <div class="col-6 col-md-4 col-xxl-3">
                <a class="block block-rounded block-link-pop text-center"
                    href="<?= base_url('settings/' . $cat) ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center text-center">
                            <div>
                                <i class="fa fa-2x <?= esc($info['icon']) ?> text-modern"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">
                                    <?= esc($info['title']) ?>
                                </div>
                                <div class="fs-xs text-muted mt-1">
                                    <?= esc($info['description']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>

<?= $this->endSection() ?>

<?= $this->section('other-styles') ?>

<?= $this->endSection() ?>