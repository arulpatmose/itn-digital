<?php /** @var array $participants */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Participants</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('participants.create')): ?>
                            <a href="<?= base_url('participants/create') ?>" class="btn btn-sm btn-primary">Add Participant</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-participants">
                            <thead>
                                <tr>
                                    <th class="text-center noOrder">#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Linked User</th>
                                    <th>Notes</th>
                                    <th class="text-center noOrder">Status</th>
                                    <th class="text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participants as $i => $p): ?>
                                    <tr>
                                        <td class="text-muted text-center"><?= $i + 1 ?></td>
                                        <td><strong><?= esc($p['name']) ?></strong></td>
                                        <td>
                                            <?php
                                            $typeClass = match($p['type']) {
                                                'staff'      => 'bg-primary',
                                                'producer'   => 'bg-info',
                                                'librarian'  => 'bg-warning text-dark',
                                                default      => 'bg-secondary',
                                            };
                                            ?>
                                            <span class="badge <?= $typeClass ?>"><?= ucfirst(esc($p['type'])) ?></span>
                                        </td>
                                        <td><?= $p['user_name'] ? esc($p['user_name']) : '<span class="text-muted">—</span>' ?></td>
                                        <td class="text-muted small"><?= esc($p['notes'] ?? '—') ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $p['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if (auth()->user()->can('participants.edit')): ?>
                                                    <a href="<?= base_url('participants/edit/' . $p['id']) ?>"
                                                       class="btn btn-sm btn-success" title="Edit"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (auth()->user()->can('participants.delete')): ?>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-participant"
                                                        data-id="<?= $p['id'] ?>" data-name="<?= esc($p['name']) ?>"
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

    var table = $('#table-participants').DataTable({
        pagingType: 'full_numbers',
        pageLength: 25,
        autoWidth: false,
        responsive: true,
        stateSave: true,
        order: [[1, 'asc']],
        columnDefs: [{ targets: 'noOrder', orderable: false }],
        drawCallback: function () {
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }
    });

    $(document).on('click', '.btn-delete-participant', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name');
        Swal.fire({
            icon: 'warning',
            title: 'Delete ' + name + '?',
            text: 'This cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#dc3545',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '<?= base_url('participants/delete') ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        table.row($('.btn-delete-participant[data-id="' + id + '"]').closest('tr')).remove().draw(false);
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
