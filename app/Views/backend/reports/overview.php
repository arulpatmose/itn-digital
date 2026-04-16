<?php

/** @var array $chips, $participants */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">

    <!-- Summary cards -->
    <div class="row">
        <?php
        $byType = array_count_values(array_column($chips, 'chip_type'));
        $unassigned = count(array_filter($chips, fn($c) => empty($c['holder_name'])));
        $total = count($chips);
        ?>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-primary"><?= $total ?></div>
                    <div class="text-muted">Total Chips</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-success"><?= $total - $unassigned ?></div>
                    <div class="text-muted">Assigned</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-warning"><?= $unassigned ?></div>
                    <div class="text-muted">Unassigned</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-info"><?= count($participants) ?></div>
                    <div class="text-muted">Participants</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chips table -->
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">All Chips — Current State</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-overview">
                            <thead>
                                <tr>
                                    <th class="text-center noOrder">#</th>
                                    <th>Chip Code</th>
                                    <th>Type</th>
                                    <th>Current Holder</th>
                                    <th>Holder Type</th>
                                    <th class="text-center noOrder">History</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chips as $i => $chip): ?>
                                    <tr>
                                        <td class="text-muted text-center"><?= $i + 1 ?></td>
                                        <td><strong><?= esc($chip['chip_code']) ?></strong></td>
                                        <td>
                                            <?php
                                            $typeClass = match ($chip['chip_type']) {
                                                'SXS'     => 'bg-primary',
                                                'SD'      => 'bg-info',
                                                'MicroSD' => 'bg-warning text-dark',
                                                default   => 'bg-secondary',
                                            };
                                            ?>
                                            <span class="badge <?= $typeClass ?>"><?= esc($chip['chip_type']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($chip['holder_type'] === 'ingestor'): ?>
                                                <i class="fa fa-building fa-fw text-muted"></i> ITN Digital
                                            <?php elseif ($chip['holder_type'] === 'librarian'): ?>
                                                <i class="fa fa-book fa-fw text-muted"></i> Library
                                            <?php elseif ($chip['holder_name']): ?>
                                                <?= esc($chip['holder_name']) ?>
                                            <?php elseif (($chip['last_tx_type'] ?? '') === 'INGEST' && ($chip['ingest_session_title'] ?? '')): ?>
                                                <span class="text-warning"><i class="fa fa-download fa-fw"></i> At Ingest: <?= esc($chip['ingest_session_title']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $chip['holder_type'] ? '<span class="badge bg-secondary">' . esc($chip['holder_type'] === 'librarian' ? 'library' : $chip['holder_type']) . '</span>' : ($chip['last_tx_type'] === 'INGEST' ? '<span class="badge bg-warning text-dark">ingest</span>' : '—') ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('reports/chip-history/' . $chip['id']) ?>"
                                                class="btn btn-sm btn-alt-secondary">
                                                <i class="fa fa-history"></i> Timeline
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    $(function() {
        $('#table-overview').DataTable({
            pagingType: 'full_numbers',
            pageLength: 25,
            autoWidth: false,
            responsive: true,
            stateSave: true,
            order: [
                [1, 'asc']
            ],
            columnDefs: [{
                targets: 'noOrder',
                orderable: false
            }],
            drawCallback: function() {
                var api = this.api();
                var start = api.page.info().start;
                api.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = start + i + 1;
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>