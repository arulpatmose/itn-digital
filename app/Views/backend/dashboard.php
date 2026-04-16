<?php /** @var array $totals */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">

    <?php $user = auth()->user(); ?>

    <!-- Quick Actions -->
    <h2 class="content-heading">Quick Actions</h2>
    <div class="row push">
        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
            <a class="block block-rounded block-link-pop text-center" href="<?= site_url('daily-schedule') ?>">
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

        <?php if ($user->can('booking.access')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('bookings') ?>">
                    <div class="block-content block-content-full ratio ratio-1x1">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-calendar-check text-success"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">Bookings</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user->can('booking.create')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('bookings/create') ?>">
                    <div class="block-content block-content-full ratio ratio-1x1">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-calendar-plus text-primary"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">New Booking</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user->can('transactions.receive')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('transactions/receive') ?>">
                    <div class="block-content block-content-full ratio ratio-1x1">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-inbox text-warning"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">Receive Chips</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user->can('transactions.ingest')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('transactions/ingest') ?>">
                    <div class="block-content block-content-full ratio ratio-1x1">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-download text-info"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">Ingest Chips</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user->can('ingest.reports')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('reports/chips-overview') ?>">
                    <div class="block-content block-content-full ratio ratio-1x1">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-chart-bar text-danger"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">Chip Reports</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scheduling Stats -->
    <?php if ($user->can('schedule.access')): ?>
        <h2 class="content-heading">Scheduling</h2>
        <div class="row push">
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('clients') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-body-color-dark"><?= number_format($totals['clients']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Clients</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('commercials') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-body-color-dark"><?= number_format($totals['commercials']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Commercials</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('schedules') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-body-color-dark"><?= number_format($totals['schedules']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Schedules</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-success"><?= number_format($totals['publishedSchedules']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Published</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-body-color-dark"><?= number_format($totals['scheduleItems']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Scheduled Ads</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="javascript:void(0)">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-success"><?= number_format($totals['publishedScheduleItems']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Ads Published</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bookings Stats -->
    <?php if ($user->can('booking.access')): ?>
        <h2 class="content-heading">Bookings</h2>
        <div class="row push">
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('bookings') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-body-color-dark"><?= number_format($totals['bookings_total']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Total</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('bookings') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-primary"><?= number_format($totals['bookings_today']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Today</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php if ($user->can('booking.approve')): ?>
                <div class="col-6 col-md-3 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center" href="<?= base_url('bookings') ?>">
                        <div class="block-content block-content-full ratio ratio-16x9">
                            <div class="d-flex justify-content-center align-items-center">
                                <div>
                                    <div class="fs-2 fw-bold <?= $totals['bookings_pending'] > 0 ? 'text-warning' : 'text-body-color-dark' ?>"><?= number_format($totals['bookings_pending']) ?></div>
                                    <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Pending Approval</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Chip Tracking Stats -->
    <?php if ($user->can('chips.view')): ?>
        <h2 class="content-heading">Chip Tracking</h2>
        <div class="row push">
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('chips') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-body-color-dark"><?= number_format($totals['chips_total']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Total Chips</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('chips') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-success"><?= number_format($totals['chips_library']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">In Library</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('chips') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-warning"><?= number_format($totals['chips_producers']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">With Producers</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-pop text-center" href="<?= base_url('chips') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <div class="fs-2 fw-bold text-info"><?= number_format($totals['chips_ingestors']) ?></div>
                                <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">With Ingestors</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php if ($user->can('ingest_sessions.view')): ?>
                <div class="col-6 col-md-3 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center" href="<?= base_url('ingest-sessions') ?>">
                        <div class="block-content block-content-full ratio ratio-16x9">
                            <div class="d-flex justify-content-center align-items-center">
                                <div>
                                    <div class="fs-2 fw-bold <?= $totals['sessions_open'] > 0 ? 'text-danger' : 'text-body-color-dark' ?>"><?= number_format($totals['sessions_open']) ?></div>
                                    <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Open Sessions</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <a class="block block-rounded block-link-pop text-center" href="<?= base_url('ingest-sessions') ?>">
                        <div class="block-content block-content-full ratio ratio-16x9">
                            <div class="d-flex justify-content-center align-items-center">
                                <div>
                                    <div class="fs-2 fw-bold <?= $totals['sessions_partial'] > 0 ? 'text-warning' : 'text-body-color-dark' ?>"><?= number_format($totals['sessions_partial']) ?></div>
                                    <div class="fs-sm fw-semibold mt-1 text-uppercase text-muted">Partial Sessions</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
