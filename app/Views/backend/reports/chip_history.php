<?php /** @var array $chip, $timeline */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12 col-lg-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Chip — <?= esc($chip['chip_code']) ?></h3>
                    <div class="block-options">
                        <a href="<?= base_url('reports/chips-overview') ?>" class="btn btn-sm btn-alt-secondary">Back</a>
                    </div>
                </div>
                <div class="block-content">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Code</dt>
                        <dd class="col-sm-8"><strong><?= esc($chip['chip_code']) ?></strong></dd>

                        <dt class="col-sm-4">Type</dt>
                        <dd class="col-sm-8">
                            <?php
                            $typeClass = match($chip['chip_type']) {
                                'SXS'     => 'bg-primary',
                                'SD'      => 'bg-info',
                                'MicroSD' => 'bg-warning text-dark',
                                default   => 'bg-secondary',
                            };
                            ?>
                            <span class="badge <?= $typeClass ?>"><?= esc($chip['chip_type']) ?></span>
                        </dd>

                        <dt class="col-sm-4">Holder</dt>
                        <dd class="col-sm-8">
                            <?= $chip['holder_name']
                                ? esc($chip['holder_name']) . ' <span class="badge bg-secondary">' . esc($chip['holder_type']) . '</span>'
                                : '<span class="text-muted">Unassigned</span>' ?>
                        </dd>

                        <dt class="col-sm-4">Transactions</dt>
                        <dd class="col-sm-8"><?= count($timeline) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Full Transaction Timeline</h3>
                </div>
                <div class="block-content block-content-full">
                    <?php if (empty($timeline)): ?>
                        <p class="text-muted py-3">No transactions recorded for this chip.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-history">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Session</th>
                                    <th>Handled By</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($timeline as $i => $tx): ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td class="text-nowrap"><?= date('d M Y H:i', strtotime($tx['created_at'])) ?></td>
                                        <td>
                                            <?php
                                            $txClass = match($tx['transaction_type']) {
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
                                        <td><?= $tx['from_name']     ? esc($tx['from_name'])     : '<span class="text-muted">—</span>' ?></td>
                                        <td><?= $tx['to_name']       ? esc($tx['to_name'])       : '<span class="text-muted">—</span>' ?></td>
                                        <td><?= $tx['session_title'] ? esc($tx['session_title']) : '<span class="text-muted">—</span>' ?></td>
                                        <td><?= esc($tx['handler_name'] ?? '—') ?></td>
                                        <td class="text-muted small"><?= esc($tx['remarks'] ?? '—') ?></td>
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
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
$(function () {
    if ($('#table-history').length) {
        $('#table-history').DataTable({
            pagingType: 'full_numbers',
            pageLength: 25,
            order: [[1, 'desc']],
            autoWidth: false,
            responsive: true,
        });
    }
});
</script>
<?= $this->endSection() ?>
