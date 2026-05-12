<?php

/** @var array $totals */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">

    <?php $user = auth()->user(); ?>

    <!-- Quick Actions -->
    <h2 class="content-heading">Quick Actions</h2>
    <div class="row">
        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
            <a class="block block-rounded block-link-shadow bg-primary text-white text-center" href="<?= site_url('daily-schedule') ?>">
                <div class="block-content block-content-full ratio ratio-16x9">
                    <div class="d-flex justify-content-center align-items-center">
                        <div>
                            <i class="fa fa-2x fa-table-list"></i>
                            <div class="fs-sm fw-semibold mt-3 text-uppercase">Ad Schedule</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <?php if ($user->can('booking.access')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-shadow bg-success text-white text-center" href="<?= base_url('bookings') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-calendar-check"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">Bookings</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user->can('booking.create')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-shadow bg-info text-white text-center" href="<?= base_url('bookings/create') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-calendar-plus"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">New Booking</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user->can('transactions.receive')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-shadow bg-warning text-white text-center" href="<?= base_url('transactions/receive') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-inbox"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">Receive Chips</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user->can('transactions.ingest')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-shadow bg-danger text-white text-center" href="<?= base_url('transactions/ingest') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-download"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">Ingest Chips</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($user->can('ingest.reports')): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a class="block block-rounded block-link-shadow bg-dark text-white text-center" href="<?= base_url('reports/chips-overview') ?>">
                    <div class="block-content block-content-full ratio ratio-16x9">
                        <div class="d-flex justify-content-center align-items-center">
                            <div>
                                <i class="fa fa-2x fa-chart-bar"></i>
                                <div class="fs-sm fw-semibold mt-3 text-uppercase">Chip Reports</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // Small renderer for an individual OneUI stat tile — keeps the markup DRY.
    $renderTile = function (string $label, $value, string $icon, string $iconClass, string $href, string $linkText, string $valueClass = '', string $colClass = 'col-12 col-md-4 col-xl-3') {
    ?>
        <div class="<?= $colClass ?>">
            <div class="block block-rounded d-flex flex-column h-100 mb-0">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="fs-3 fw-bold <?= $valueClass ?>"><?= number_format((int) $value) ?></dt>
                        <dd class="fs-sm fw-medium text-muted mb-0"><?= esc($label) ?></dd>
                    </dl>
                    <div class="item item-rounded-lg bg-body-light">
                        <i class="fa <?= esc($icon) ?> fs-3 <?= esc($iconClass) ?>"></i>
                    </div>
                </div>
                <div class="bg-body-light rounded-bottom">
                    <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between" href="<?= esc($href) ?>">
                        <span><?= esc($linkText) ?></span>
                        <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php
    };

    // Inside the col-md-8 stats column the available width is narrower, so use 2/3-per-row.
    $tileColInner = 'col-12 col-md-6 col-xl-4';
    ?>

    <?php if ($user->can('schedule.access')): ?>
        <h2 class="content-heading"><i class="fa fa-fw fa-table-list text-modern me-1"></i> Scheduling</h2>

        <!-- 3-column row: chart + related stat tiles stacked in each column -->
        <div class="row items-push">
            <div class="col-12 col-md-4">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Commercials <small class="fw-normal text-muted">— last 14 days</small></h3>
                        <div class="block-options">
                            <span class="badge bg-info-light text-info"><?= number_format($totals['commercials'] ?? 0) ?> total</span>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="position-relative" style="height: 200px;">
                            <canvas id="chart-commercials"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row items-push">
                    <?php
                    $renderTile('Clients',     $totals['clients'],     'fa-handshake', 'text-primary', base_url('clients'),     'View all clients',     '', 'col-12 col-md-6');
                    $renderTile('Commercials', $totals['commercials'], 'fa-film',      'text-info',    base_url('commercials'), 'View all commercials', '', 'col-12 col-md-6');
                    ?>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Schedules <small class="fw-normal text-muted">— last 14 days</small></h3>
                        <div class="block-options">
                            <span class="badge bg-info-light text-info"><?= number_format($totals['schedules'] ?? 0) ?> total</span>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="position-relative" style="height: 200px;">
                            <canvas id="chart-schedules"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row items-push">
                    <?php
                    $renderTile('Schedules', $totals['schedules'],          'fa-calendar-alt', 'text-modern',  base_url('schedules'), 'View all schedules', '',             'col-12 col-md-6');
                    $renderTile('Published', $totals['publishedSchedules'], 'fa-check-double', 'text-success', base_url('schedules'), 'View published',     'text-success', 'col-12 col-md-6');
                    ?>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Schedule Items <small class="fw-normal text-muted">— last 14 days</small></h3>
                        <div class="block-options">
                            <span class="badge bg-info-light text-info"><?= number_format($totals['scheduleItems'] ?? 0) ?> total</span>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="position-relative" style="height: 200px;">
                            <canvas id="chart-schedule-items"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row items-push">
                    <?php
                    $renderTile('Ad Items',      $totals['scheduleItems'],          'fa-list-ul',  'text-secondary', base_url('daily-schedule'), 'View daily schedule', '',             'col-12 col-md-6');
                    $renderTile('Ads Published', $totals['publishedScheduleItems'], 'fa-bullhorn', 'text-success',   base_url('daily-schedule'), 'View daily schedule', 'text-success', 'col-12 col-md-6');
                    ?>
                </div>
            </div>
            <div class="col-12">
                <!-- Today's ad items added in the last 24h (commercial joined via schedule) -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Today's Scheduled Ads <small class="fw-normal text-muted">· added in the last 24h</small></h3>
                        <div class="block-options">
                            <span class="badge bg-info-light text-info"><?= number_format(count($totals['recent_schedule_additions'] ?? [])) ?> items</span>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <?php if (empty($totals['recent_schedule_additions'])): ?>
                            <div class="text-center text-muted py-4">No ad items scheduled for today were added in the last 24 hours.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-borderless table-vcenter mb-0">
                                    <thead>
                                        <tr>
                                            <th>Commercial</th>
                                            <th class="d-none d-md-table-cell">Schedule</th>
                                            <th class="d-none d-md-table-cell">Client</th>
                                            <th class="d-none d-lg-table-cell">Program</th>
                                            <th class="d-none d-lg-table-cell">Spot</th>
                                            <th class="text-center">Status</th>
                                            <th class="d-none d-md-table-cell">Added By</th>
                                            <th class="text-end">When</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($totals['recent_schedule_additions'] as $r): ?>
                                            <tr>
                                                <td class="fw-semibold">
                                                    <?php if ($r['commercial_href']): ?>
                                                        <a href="<?= esc($r['commercial_href']) ?>" class="text-reset"><?= esc($r['commercial']) ?></a>
                                                    <?php else: ?>
                                                        <?= esc($r['commercial']) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="d-none d-md-table-cell small">
                                                    <?php if ($r['schedule_href']): ?>
                                                        <a href="<?= esc($r['schedule_href']) ?>" class="text-muted"><?= esc($r['schedule_ref']) ?></a>
                                                    <?php else: ?>
                                                        <span class="text-muted"><?= esc($r['schedule_ref']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="d-none d-md-table-cell small text-muted"><?= esc($r['client']) ?></td>
                                                <td class="d-none d-lg-table-cell small text-muted"><?= esc($r['program']) ?></td>
                                                <td class="d-none d-lg-table-cell small text-muted"><?= esc($r['spot']) ?></td>
                                                <td class="text-center">
                                                    <?php if ($r['published']): ?>
                                                        <span class="badge bg-success">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Draft</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="d-none d-md-table-cell small text-muted"><?= esc($r['added_by'] ?? '—') ?></td>
                                                <td class="text-end small text-muted text-nowrap"><?= date('d M H:i', strtotime($r['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($user->can('chips.view')): ?>
        <h2 class="content-heading"><i class="fa fa-fw fa-microchip text-success me-1"></i> Chip Inventory</h2>
        <div class="row items-push">
            <div class="col-12 col-md-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Chip Transactions <small class="fw-normal text-muted">— last 14 days</small></h3>
                        <div class="block-options">
                            <span class="badge bg-info-light text-info"><?= number_format($totals['tx_week'] ?? 0) ?> this week</span>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="position-relative" style="height: 240px;">
                            <canvas id="chart-tx"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8">
                <div class="row items-push">
                    <?php
                    $renderTile('Total Chips',     $totals['chips_total'],        'fa-microchip',  'text-modern',  base_url('chips'), 'View all chips',      '',             $tileColInner);
                    $renderTile('In Library',      $totals['chips_library'],      'fa-book',       'text-success', base_url('chips'), 'View library chips',  'text-success', $tileColInner);
                    $renderTile('With Producers',  $totals['chips_producers'],    'fa-user-tie',   'text-warning', base_url('chips'), 'View producer chips', 'text-warning', $tileColInner);
                    $renderTile('At ITN Digital',  $totals['chips_digital_unit'], 'fa-building',   'text-info',    base_url('chips'), 'View ITN chips',      'text-info',    $tileColInner);

                    if ($user->can('ingest_sessions.view')) {
                        $openClass = ($totals['sessions_open'] ?? 0) > 0 ? 'text-danger' : '';
                        $renderTile('Open Sessions', $totals['sessions_open'] ?? 0, 'fa-folder-open', 'text-danger', base_url('ingest-sessions'), 'View open sessions', $openClass, $tileColInner);
                    }

                    $renderTile('Transactions Today', $totals['tx_today'] ?? 0, 'fa-exchange-alt', 'text-success', base_url('transactions'), "View today's transactions", 'text-success', $tileColInner);
                    ?>
                </div>
            </div>
        </div>

        <!-- Chip Distribution (left) + Recent Chip Transactions (right) -->
        <div class="row items-push">
            <div class="col-12 col-md-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Chip Distribution</h3>
                        <div class="block-options">
                            <a href="<?= base_url('reports/chips-overview') ?>" class="btn-block-option"><i class="fa fa-chart-pie"></i></a>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="position-relative" style="height: 220px;">
                            <canvas id="chart-chip-distribution"></canvas>
                        </div>
                        <ul class="list-unstyled mt-3 mb-0 small">
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span><i class="fa fa-circle text-success me-1"></i>In Library</span>
                                <strong><?= number_format($totals['chips_library'] ?? 0) ?></strong>
                            </li>
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span><i class="fa fa-circle text-warning me-1"></i>With Producers</span>
                                <strong><?= number_format($totals['chips_producers'] ?? 0) ?></strong>
                            </li>
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span><i class="fa fa-circle text-info me-1"></i>At ITN Digital</span>
                                <strong><?= number_format($totals['chips_digital_unit'] ?? 0) ?></strong>
                            </li>
                            <li class="d-flex justify-content-between py-1">
                                <span><i class="fa fa-circle text-muted me-1"></i>Unassigned</span>
                                <strong><?= number_format($totals['chips_unassigned'] ?? 0) ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Recent Chip Transactions</h3>
                        <div class="block-options">
                            <a href="<?= base_url('transactions') ?>" class="btn-block-option">View all <i class="fa fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <?php if (empty($totals['recent_transactions'])): ?>
                            <div class="text-center text-muted py-4">No transactions yet.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-borderless table-vcenter mb-0">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th class="text-center">Chips</th>
                                            <th>From → To</th>
                                            <th class="d-none d-md-table-cell">Handler</th>
                                            <th class="text-end">When</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($totals['recent_transactions'] as $tx): ?>
                                            <?php
                                            $txClass = match ($tx['transaction_type']) {
                                                'RECEIVE'  => 'bg-success',
                                                'TRANSFER' => 'bg-info',
                                                'HANDOVER' => 'bg-warning',
                                                'INGEST'   => 'bg-primary',
                                                default    => 'bg-secondary',
                                            };
                                            $toLabel = match ($tx['to_location'] ?? null) {
                                                'digital_unit' => 'ITN Digital',
                                                'library'      => 'Library',
                                                'ingest'       => $tx['session_title'] ? 'Ingest: ' . $tx['session_title'] : 'Ingest',
                                                'producer'     => $tx['to_name'] ?: '—',
                                                default        => '—',
                                            };
                                            $fromLabel = $tx['from_name'] ?: '<span class="text-muted">—</span>';
                                            ?>
                                            <tr>
                                                <td><span class="badge <?= $txClass ?>"><?= esc($tx['transaction_type']) ?></span></td>
                                                <td class="text-center fw-semibold"><?= (int) $tx['chip_count'] ?></td>
                                                <td class="small">
                                                    <?= $fromLabel ?>
                                                    <i class="fa fa-arrow-right text-muted mx-1"></i>
                                                    <?= esc($toLabel) ?>
                                                </td>
                                                <td class="d-none d-md-table-cell small text-muted"><?= esc($tx['handler_name'] ?? '—') ?></td>
                                                <td class="text-end small text-muted text-nowrap"><?= date('d M H:i', strtotime($tx['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($user->can('booking.access')): ?>
        <h2 class="content-heading"><i class="fa fa-fw fa-calendar-check text-primary me-1"></i> Bookings</h2>
        <div class="row items-push">
            <div class="col-12 col-md-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Bookings <small class="fw-normal text-muted">— last 14 days</small></h3>
                        <div class="block-options">
                            <span class="badge bg-info-light text-info"><?= number_format($totals['bookings_total'] ?? 0) ?> total</span>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="position-relative" style="height: 240px;">
                            <canvas id="chart-bookings"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8">
                <div class="row items-push">
                    <?php
                    $renderTile('Total Bookings',  $totals['bookings_total'] ?? 0, 'fa-calendar-check', 'text-primary', base_url('bookings'), 'View all bookings',     '',          $tileColInner);
                    $renderTile('Bookings Today',  $totals['bookings_today'] ?? 0, 'fa-calendar-day',   'text-info',    base_url('bookings'), "View today's bookings", 'text-info', $tileColInner);
                    if ($user->can('booking.approve')) {
                        $pendingClass = ($totals['bookings_pending'] ?? 0) > 0 ? 'text-warning' : '';
                        $renderTile('Pending Approval', $totals['bookings_pending'] ?? 0, 'fa-hourglass-half', 'text-warning', base_url('bookings'), 'Review pending', $pendingClass, $tileColInner);
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script src="<?= base_url('assets/js/plugins/chart.js/chart.umd.min.js') ?>"></script>
<script>
    $(function() {
        // Shared options
        const gridColor = 'rgba(0,0,0,0.05)';
        const baseOpts = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 12
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        color: gridColor
                    }
                }
            }
        };

        const dayLabels = <?= json_encode($totals['chart_day_labels'] ?? []) ?>;

        <?php if ($user->can('chips.view')): ?>
            // Chip transactions stacked area
            const txCtx = document.getElementById('chart-tx');
            if (txCtx) {
                new Chart(txCtx, {
                    type: 'bar',
                    data: {
                        labels: dayLabels,
                        datasets: [{
                                label: 'Receive',
                                data: <?= json_encode($totals['chart_tx_series']['RECEIVE']  ?? []) ?>,
                                backgroundColor: 'rgba(40,167,69,0.85)'
                            },
                            {
                                label: 'Transfer',
                                data: <?= json_encode($totals['chart_tx_series']['TRANSFER'] ?? []) ?>,
                                backgroundColor: 'rgba(23,162,184,0.85)'
                            },
                            {
                                label: 'Handover',
                                data: <?= json_encode($totals['chart_tx_series']['HANDOVER'] ?? []) ?>,
                                backgroundColor: 'rgba(255,193,7,0.85)'
                            },
                            {
                                label: 'Ingest',
                                data: <?= json_encode($totals['chart_tx_series']['INGEST']   ?? []) ?>,
                                backgroundColor: 'rgba(0,123,255,0.85)'
                            }
                        ]
                    },
                    options: {
                        ...baseOpts,
                        scales: {
                            x: {
                                ...baseOpts.scales.x,
                                stacked: true
                            },
                            y: {
                                ...baseOpts.scales.y,
                                stacked: true
                            }
                        }
                    }
                });
            }

            // Chip distribution donut
            const distCtx = document.getElementById('chart-chip-distribution');
            if (distCtx) {
                new Chart(distCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['In Library', 'With Producers', 'At ITN Digital', 'Unassigned'],
                        datasets: [{
                            data: [
                                <?= (int) ($totals['chips_library']      ?? 0) ?>,
                                <?= (int) ($totals['chips_producers']    ?? 0) ?>,
                                <?= (int) ($totals['chips_digital_unit'] ?? 0) ?>,
                                <?= (int) ($totals['chips_unassigned']   ?? 0) ?>
                            ],
                            backgroundColor: ['rgba(40,167,69,0.85)', 'rgba(255,193,7,0.85)', 'rgba(23,162,184,0.85)', 'rgba(108,117,125,0.6)'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        <?php endif; ?>

        <?php if ($user->can('booking.access')): ?>
            const bkCtx = document.getElementById('chart-bookings');
            if (bkCtx) {
                new Chart(bkCtx, {
                    type: 'line',
                    data: {
                        labels: dayLabels,
                        datasets: [{
                            label: 'Bookings',
                            data: <?= json_encode($totals['chart_bookings'] ?? []) ?>,
                            borderColor: 'rgba(0,123,255,1)',
                            backgroundColor: 'rgba(0,123,255,0.15)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2
                        }]
                    },
                    options: baseOpts
                });
            }
        <?php endif; ?>

        <?php if ($user->can('schedule.access')): ?>
            const commercialsCtx = document.getElementById('chart-commercials');
            if (commercialsCtx) {
                new Chart(commercialsCtx, {
                    type: 'line',
                    data: {
                        labels: dayLabels,
                        datasets: [{
                            label: 'Commercials',
                            data: <?= json_encode($totals['chart_commercials'] ?? []) ?>,
                            borderColor: 'rgba(23,162,184,1)',
                            backgroundColor: 'rgba(23,162,184,0.15)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2
                        }]
                    },
                    options: baseOpts
                });
            }

            const schCtx = document.getElementById('chart-schedules');
            if (schCtx) {
                new Chart(schCtx, {
                    type: 'line',
                    data: {
                        labels: dayLabels,
                        datasets: [{
                            label: 'Schedules',
                            data: <?= json_encode($totals['chart_schedules'] ?? []) ?>,
                            borderColor: 'rgba(91,71,148,1)',
                            backgroundColor: 'rgba(91,71,148,0.15)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2
                        }]
                    },
                    options: baseOpts
                });
            }

            const schItemsCtx = document.getElementById('chart-schedule-items');
            if (schItemsCtx) {
                new Chart(schItemsCtx, {
                    type: 'line',
                    data: {
                        labels: dayLabels,
                        datasets: [{
                            label: 'Schedule Items',
                            data: <?= json_encode($totals['chart_schedule_items'] ?? []) ?>,
                            borderColor: 'rgba(40,167,69,1)',
                            backgroundColor: 'rgba(40,167,69,0.15)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2
                        }]
                    },
                    options: baseOpts
                });
            }
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>