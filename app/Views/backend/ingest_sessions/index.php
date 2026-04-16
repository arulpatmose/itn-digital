<?php /** @var array $sessions */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Ingest Sessions</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('ingest_sessions.create')): ?>
                            <a href="<?= base_url('ingest-sessions/create') ?>" class="btn btn-sm btn-primary">New Session</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-sessions">
                            <thead>
                                <tr>
                                    <th class="text-center noOrder">#</th>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th class="text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $i => $s): ?>
                                    <tr>
                                        <td class="text-muted text-center"><?= $i + 1 ?></td>
                                        <td><strong><?= esc($s['title']) ?></strong>
                                            <?php if (!empty($s['description'])): ?>
                                                <br><small class="text-muted"><?= esc($s['description']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($s['ingest_location'] ?? '—') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = match($s['status']) {
                                                'open'    => 'bg-success',
                                                'partial' => 'bg-warning text-dark',
                                                'closed'  => 'bg-secondary',
                                                default   => 'bg-dark',
                                            };
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= ucfirst(esc($s['status'])) ?></span>
                                        </td>
                                        <td><?= esc($s['creator_name'] ?? '—') ?></td>
                                        <td class="text-nowrap"><?= date('d M Y H:i', strtotime($s['created_at'])) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('ingest-sessions/' . $s['id']) ?>"
                                               class="btn btn-sm btn-alt-secondary" title="View"
                                               data-bs-toggle="tooltip">
                                                <i class="fa fa-eye"></i>
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
$(function () {
    <?php if ($flash = session()->getFlashdata('success')): ?>
    Swal.fire({ icon: 'success', title: 'Success', text: <?= json_encode($flash) ?>, confirmButtonColor: '#28a745' });
    <?php endif; ?>
    <?php if ($flash = session()->getFlashdata('error')): ?>
    Swal.fire({ icon: 'error', title: 'Error', text: <?= json_encode($flash) ?>, confirmButtonColor: '#dc3545' });
    <?php endif; ?>

    $('#table-sessions').DataTable({
        pagingType: 'full_numbers',
        pageLength: 25,
        autoWidth: false,
        responsive: true,
        stateSave: true,
        order: [[5, 'desc']],
        columnDefs: [{ targets: 'noOrder', orderable: false }],
        drawCallback: function () {
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
