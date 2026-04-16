<?php /** @var array $chips */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">All Chips</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('chips.create')): ?>
                            <a href="<?= base_url('chips/create') ?>" class="btn btn-sm btn-primary">Register Chip</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-chips">
                            <thead>
                                <tr>
                                    <th class="text-center noOrder">#</th>
                                    <th>Chip Code</th>
                                    <th>Type</th>
                                    <th>Current Holder</th>
                                    <th>Holder Type</th>
                                    <th>Notes</th>
                                    <th class="text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chips as $i => $chip): ?>
                                    <tr>
                                        <td class="text-muted text-center"><?= $i + 1 ?></td>
                                        <td><strong><?= esc($chip['chip_code']) ?></strong></td>
                                        <td>
                                            <?php
                                            $typeClass = match($chip['chip_type']) {
                                                'SXS'     => 'bg-primary',
                                                'SD'      => 'bg-info',
                                                'MicroSD' => 'bg-warning text-dark',
                                                default   => 'bg-secondary',
                                            };
                                            ?>
                                            <span class="badge <?= $typeClass ?>"><?= esc($chip['chip_type']) ?></span>
                                        </td>
                                        <td><?= $chip['holder_name'] ? esc($chip['holder_name']) : '<span class="text-muted">—</span>' ?></td>
                                        <td><?= $chip['holder_type'] ? '<span class="badge bg-secondary">' . esc($chip['holder_type']) . '</span>' : '—' ?></td>
                                        <td class="text-muted small"><?= esc($chip['notes'] ?? '—') ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="<?= base_url('chips/detail/' . $chip['id']) ?>"
                                                   class="btn btn-sm btn-alt-secondary" title="View History"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fa fa-history"></i>
                                                </a>
                                                <?php if (auth()->user()->can('chips.edit')): ?>
                                                    <a href="<?= base_url('chips/edit/' . $chip['id']) ?>"
                                                       class="btn btn-sm btn-success" title="Edit"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (auth()->user()->can('chips.delete')): ?>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-chip"
                                                        data-id="<?= $chip['id'] ?>" data-code="<?= esc($chip['chip_code']) ?>"
                                                        data-bs-toggle="tooltip" title="Delete">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
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

    var chipTable = $('#table-chips').DataTable({
        pagingType: 'full_numbers',
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        autoWidth: false,
        responsive: true,
        stateSave: true,
        order: [[1, 'asc']],
        columnDefs: [
            { targets: 'noOrder', orderable: false },
        ],
        drawCallback: function () {
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }
    });

    $(document).on('click', '.btn-delete-chip', function () {
        var id   = $(this).data('id');
        var code = $(this).data('code');
        Swal.fire({
            icon: 'warning',
            title: 'Delete chip ' + code + '?',
            text: 'This will remove the chip record. Transaction history will be preserved.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#dc3545',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '<?= base_url('chips/delete') ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        chipTable.row($('.btn-delete-chip[data-id="' + id + '"]').closest('tr')).remove().draw(false);
                        toast.fire({ icon: 'success', title: res.message });
                    } else {
                        toast.fire({ icon: 'error', title: res.message });
                    }
                },
                error: function () { toast.fire({ icon: 'error', title: 'An error occurred.' }); }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
