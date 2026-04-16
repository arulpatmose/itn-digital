<?php

/** @var array $transactions */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Quick Actions</h3>
                </div>
                <div class="block-content">
                    <div class="row g-3 pb-3">
                        <?php if (auth()->user()->can('transactions.receive')): ?>
                            <div class="col-6 col-md-3">
                                <a href="<?= base_url('transactions/receive') ?>" class="btn btn-success w-100 py-3">
                                    <i class="fa fa-arrow-circle-down d-block fs-3 mb-1"></i>
                                    Receive
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (auth()->user()->can('transactions.transfer')): ?>
                            <div class="col-6 col-md-3">
                                <a href="<?= base_url('transactions/transfer') ?>" class="btn btn-info w-100 py-3">
                                    <i class="fa fa-exchange-alt d-block fs-3 mb-1"></i>
                                    Transfer
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (auth()->user()->can('transactions.handover')): ?>
                            <div class="col-6 col-md-3">
                                <a href="<?= base_url('transactions/handover') ?>" class="btn btn-warning w-100 py-3">
                                    <i class="fa fa-hand-holding d-block fs-3 mb-1"></i>
                                    Handover
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (auth()->user()->can('transactions.ingest')): ?>
                            <div class="col-6 col-md-3">
                                <a href="<?= base_url('transactions/ingest') ?>" class="btn btn-primary w-100 py-3">
                                    <i class="fa fa-layer-group d-block fs-3 mb-1"></i>
                                    Ingest
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Log -->
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Transaction Log</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-transactions">
                            <thead>
                                <tr>
                                    <th class="text-center noOrder">#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Chips</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Session</th>
                                    <th>Handled By</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $i => $tx): ?>
                                    <tr>
                                        <td class="text-muted text-center"><?= $i + 1 ?></td>
                                        <td class="text-nowrap"><?= date('d M Y H:i', strtotime($tx['created_at'])) ?></td>
                                        <td>
                                            <?php
                                            $txClass = match ($tx['transaction_type']) {
                                                'RECEIVE'  => 'bg-success',
                                                'TRANSFER' => 'bg-info',
                                                'HANDOVER' => 'bg-warning text-dark',
                                                'INGEST'   => 'bg-primary',
                                                'RETURN'   => 'bg-secondary',
                                                default    => 'bg-dark',
                                            };
                                            ?>
                                            <span class="badge <?= $txClass ?>"><?= esc($tx['transaction_type']) ?></span>
                                        </td>
                                        <td><?= (int) $tx['chip_count'] ?></td>
                                        <td><?= $tx['from_name'] ? esc($tx['from_name']) : '<span class="text-muted">—</span>' ?></td>
                                        <td><?= $tx['to_name']   ? esc($tx['to_name'])   : '<span class="text-muted">—</span>' ?></td>
                                        <td>
                                            <?php if ($tx['session_title'] && $tx['ingest_session_id']): ?>
                                                <a href="<?= base_url('ingest-sessions/' . $tx['ingest_session_id']) ?>">
                                                    <?= esc($tx['session_title']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($tx['handler_name'] ?? '—') ?></td>
                                        <td class="text-muted small"><?= esc($tx['remarks'] ?? '—') ?></td>
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
        <?php if ($flash = session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#28a745'
            });
        <?php endif; ?>
        <?php if ($flash = session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#dc3545'
            });
        <?php endif; ?>

        $('#table-transactions').DataTable({
            pagingType: 'full_numbers',
            pageLength: 25,
            autoWidth: false,
            responsive: true,
            stateSave: true,
            order: [
                [1, 'desc']
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