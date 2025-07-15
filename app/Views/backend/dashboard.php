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
<div class="content">
    <div class="row">
        <div class="col-3 col-md-3 col-xxl-3">
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
</div>
<div class="content">
    <div class="row push">
        <div class="col-6 col-md-3 col-xxl-3">
            <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                <div class="block-content block-content-full ratio ratio-16x9">
                    <div class="d-flex justify-content-center align-items-center">
                        <div>
                            <div class="fs-2 fw-bold text-body-color-dark"><?= number_format(esc($totals['clients'])) ?></div>
                            <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Clients</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3 col-xxl-3">
            <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                <div class="block-content block-content-full ratio ratio-16x9">
                    <div class="d-flex justify-content-center align-items-center">
                        <div>
                            <div class="fs-2 fw-bold text-body-color-dark"><?= number_format(esc($totals['commercials'])) ?></div>
                            <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Commercials</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3 col-xxl-3">
            <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                <div class="block-content block-content-full ratio ratio-16x9">
                    <div class="d-flex justify-content-center align-items-center">
                        <div>
                            <div class="fs-2 fw-bold text-body-color-dark"><?= number_format(esc($totals['schedules'])) ?></div>
                            <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Schedules</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3 col-xxl-3">
            <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                <div class="block-content block-content-full ratio ratio-16x9">
                    <div class="d-flex justify-content-center align-items-center">
                        <div>
                            <div class="fs-2 fw-bold text-body-color-dark"><?= number_format(esc($totals['scheduleItems'])) ?></div>
                            <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Scheduled Items</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>