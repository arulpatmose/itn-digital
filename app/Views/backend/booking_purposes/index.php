<?php
/**
 * Booking Purposes — inline AJAX CRUD.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Booking Purposes</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('booking_purpose.create')): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-purpose">
                                Add New
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-booking-purposes">
                            <thead>
                                <tr>
                                    <th class="rowIndex text-center noOrder">#</th>
                                    <th class="purposeGroup">Group</th>
                                    <th class="purposeName">Name</th>
                                    <th class="purposeDescription">Description</th>
                                    <th class="statusLabel text-center">Status</th>
                                    <th class="statusToggle text-center noOrder">Status Toggle</th>
                                    <th class="tableAction all text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($booking_purposes as $i => $purpose): ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td><?= esc($purpose['group_name'] ?? 'Other') ?></td>
                                        <td><?= esc($purpose['name']) ?></td>
                                        <td class="text-muted small"><?= esc($purpose['description'] ?? '—') ?></td>
                                        <td>
                                            <span class="badge <?= (int) $purpose['is_active'] === 1 ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= (int) $purpose['is_active'] === 1 ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (auth()->user()->can('booking_purpose.edit')): ?>
                                                <div class="form-check form-switch d-inline-flex justify-content-center mb-0">
                                                    <input class="form-check-input booking-purpose-status-toggle" type="checkbox" role="switch"
                                                        data-id="<?= $purpose['id'] ?>"
                                                        <?= (int) $purpose['is_active'] === 1 ? 'checked' : '' ?>
                                                        aria-label="<?= (int) $purpose['is_active'] === 1 ? 'Active' : 'Inactive' ?>">
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if (auth()->user()->can('booking_purpose.edit')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success btn-edit-purpose"
                                                        data-id="<?= $purpose['id'] ?>"
                                                        data-group-id="<?= (int) ($purpose['group_id'] ?? 0) ?>"
                                                        data-group-name="<?= esc($purpose['group_name'] ?? 'Other') ?>"
                                                        data-name="<?= esc($purpose['name']) ?>"
                                                        data-description="<?= esc($purpose['description'] ?? '') ?>"
                                                        data-active="<?= (int) $purpose['is_active'] ?>"
                                                        data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (auth()->user()->can('booking_purpose.delete')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete-purpose"
                                                        data-id="<?= $purpose['id'] ?>"
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

<div class="modal fade" id="modal-add-purpose" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Booking Purpose</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Group <span class="text-danger">*</span></label>
                    <select class="form-select" id="add-purpose-group-id">
                        <option value="">Select a group</option>
                        <?php foreach ($purpose_groups as $group): ?>
                            <option value="<?= $group['id'] ?>"><?= esc($group['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="add-purpose-name" placeholder="e.g. Meeting">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" id="add-purpose-description" rows="2"></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="add-purpose-active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-save-purpose">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-edit-purpose" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Booking Purpose</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-purpose-id">
                <div class="mb-3">
                    <label class="form-label">Group <span class="text-danger">*</span></label>
                    <select class="form-select" id="edit-purpose-group-id">
                        <option value="">Select a group</option>
                        <?php foreach ($purpose_groups as $group): ?>
                            <option value="<?= $group['id'] ?>"><?= esc($group['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-purpose-name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" id="edit-purpose-description" rows="2"></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="edit-purpose-active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-update-purpose">Update</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
$(function () {
    var canEditPurpose = <?= json_encode(auth()->user()->can('booking_purpose.edit')) ?>;
    var canDeletePurpose = <?= json_encode(auth()->user()->can('booking_purpose.delete')) ?>;
    var purposeTable = null;

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function formatPurposeStatus(isActive) {
        return '<span class="badge ' + (Number(isActive) === 1 ? 'bg-success' : 'bg-secondary') + '">'
            + (Number(isActive) === 1 ? 'Active' : 'Inactive')
            + '</span>';
    }

    function formatPurposeActions(purpose) {
        var buttons = '<div class="btn-group">';

        if (canEditPurpose) {
            buttons += '<button type="button" class="btn btn-sm btn-success btn-edit-purpose"'
                + ' data-id="' + purpose.id + '"'
                + ' data-group-id="' + Number(purpose.group_id || 0) + '"'
                + ' data-group-name="' + escapeHtml(purpose.group_name || 'Other') + '"'
                + ' data-name="' + escapeHtml(purpose.name) + '"'
                + ' data-description="' + escapeHtml(purpose.description || '') + '"'
                + ' data-active="' + Number(purpose.is_active) + '"'
                + ' data-bs-toggle="tooltip" title="Edit">'
                + '<i class="fa fa-pencil-alt"></i></button>';
        }

        if (canDeletePurpose) {
            buttons += '<button type="button" class="btn btn-sm btn-danger btn-delete-purpose"'
                + ' data-id="' + purpose.id + '"'
                + ' data-bs-toggle="tooltip" title="Delete">'
                + '<i class="fa fa-times"></i></button>';
        }

        buttons += '</div>';
        return buttons;
    }

    function formatPurposeToggle(purpose) {
        if (!canEditPurpose) {
            return '<span class="text-muted small">—</span>';
        }

        return '<div class="form-check form-switch d-inline-flex justify-content-center mb-0">'
            + '<input class="form-check-input booking-purpose-status-toggle" type="checkbox" role="switch"'
            + ' data-id="' + purpose.id + '"'
            + (Number(purpose.is_active) === 1 ? ' checked' : '')
            + ' aria-label="' + (Number(purpose.is_active) === 1 ? 'Active' : 'Inactive') + '">'
            + '</div>';
    }

    if ($('#table-booking-purposes').length) {
        purposeTable = $('#table-booking-purposes').DataTable({
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            info: true,
            order: [[1, 'asc']],
            columnDefs: [
                { targets: 'rowIndex', width: '5%', className: 'text-center', orderable: false },
                { targets: 'purposeGroup', width: '16%' },
                { targets: 'purposeName', width: '18%' },
                { targets: 'purposeDescription', width: '28%' },
                { targets: 'statusLabel', width: '12%', className: 'text-center' },
                { targets: 'statusToggle', width: '11%', className: 'text-center', orderable: false },
                { targets: 'tableAction', width: '15%', className: 'text-center', orderable: false }
            ],
            drawCallback: function () {
                var api = this.api();
                var start = api.page.info().start;
                api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                    cell.innerHTML = start + i + 1;
                });
            }
        });
    }

    $('#btn-save-purpose').on('click', function () {
        var groupId = $('#add-purpose-group-id').val();
        var name = $('#add-purpose-name').val().trim();
        var desc = $('#add-purpose-description').val().trim();
        var active = $('#add-purpose-active').val();

        if (!groupId || !name) { toast.fire({ icon: 'warning', title: 'Group and name are required.' }); return; }

        $.ajax({
            url: _AppUri + '/booking-purposes/submit',
            type: 'POST',
            data: { group_id: groupId, name: name, description: desc, is_active: active },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modal-add-purpose').modal('hide');
                    $('#add-purpose-group-id').val('');
                    $('#add-purpose-name').val('');
                    $('#add-purpose-description').val('');
                    $('#add-purpose-active').val('1');

                    if (purposeTable && res.data) {
                        purposeTable.row.add([
                            '',
                            escapeHtml(res.data.group_name || 'Other'),
                            escapeHtml(res.data.name),
                            '<span class="text-muted small">' + escapeHtml(res.data.description || '—') + '</span>',
                            formatPurposeStatus(res.data.is_active),
                            formatPurposeToggle(res.data),
                            formatPurposeActions(res.data)
                        ]).draw(false);
                    }

                    toast.fire({ icon: 'success', title: res.message });
                } else {
                    toast.fire({ icon: 'error', title: res.message });
                }
            },
            error: function () { toast.fire({ icon: 'error', title: 'An error occurred.' }); }
        });
    });

    $(document).on('click', '.btn-edit-purpose', function () {
        $('#edit-purpose-id').val($(this).data('id'));
        $('#edit-purpose-group-id').val(String($(this).data('group-id')));
        $('#edit-purpose-name').val($(this).data('name'));
        $('#edit-purpose-description').val($(this).data('description'));
        $('#edit-purpose-active').val(String($(this).data('active')));
        $('#modal-edit-purpose').modal('show');
    });

    $('#btn-update-purpose').on('click', function () {
        var id = $('#edit-purpose-id').val();
        var groupId = $('#edit-purpose-group-id').val();
        var name = $('#edit-purpose-name').val().trim();
        var desc = $('#edit-purpose-description').val().trim();
        var active = $('#edit-purpose-active').val();
        var $button = $('.btn-edit-purpose[data-id="' + id + '"]').first();

        if (!groupId || !name) { toast.fire({ icon: 'warning', title: 'Group and name are required.' }); return; }

        $.ajax({
            url: _AppUri + '/booking-purposes/update/' + id,
            type: 'POST',
            data: { group_id: groupId, name: name, description: desc, is_active: active },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modal-edit-purpose').modal('hide');
                    if (purposeTable && $button.length) {
                        var updatedPurpose = {
                            id: id,
                            group_id: groupId,
                            group_name: $('#edit-purpose-group-id option:selected').text(),
                            name: name,
                            description: desc,
                            is_active: active
                        };

                        purposeTable.row($button.closest('tr')).data([
                            '',
                            escapeHtml(updatedPurpose.group_name || 'Other'),
                            escapeHtml(updatedPurpose.name),
                            '<span class="text-muted small">' + escapeHtml(updatedPurpose.description || '—') + '</span>',
                            formatPurposeStatus(updatedPurpose.is_active),
                            formatPurposeToggle(updatedPurpose),
                            formatPurposeActions(updatedPurpose)
                        ]).draw(false);
                    }

                    toast.fire({ icon: 'success', title: res.message });
                } else {
                    toast.fire({ icon: 'error', title: res.message });
                }
            },
            error: function () { toast.fire({ icon: 'error', title: 'An error occurred.' }); }
        });
    });

    $(document).on('click', '.btn-delete-purpose', function () {
        var id = $(this).data('id');
        toast.fire({
            title: 'Delete this booking purpose?',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#dc3545',
        }).then(function (result) {
            if (result.value) {
                var $button = $('.btn-delete-purpose[data-id="' + id + '"]').first();
                $.ajax({
                    url: _AppUri + '/booking-purposes/delete',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            if (purposeTable && $button.length) {
                                purposeTable.row($button.closest('tr')).remove().draw(false);
                            }
                            toast.fire({ icon: 'success', title: res.message });
                        } else {
                            toast.fire({ icon: 'error', title: res.message });
                        }
                    },
                    error: function () { toast.fire({ icon: 'error', title: 'An error occurred.' }); }
                });
            }
        });
    });

    $(document).on('change', '.booking-purpose-status-toggle', function () {
        var id = $(this).data('id');
        var $toggle = $(this);

        $.ajax({
            url: _AppUri + '/booking-purposes/toggle-status',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    if (purposeTable && $toggle.length) {
                        var row = purposeTable.row($toggle.closest('tr'));
                        var rowData = row.data();
                        var purpose = {
                            id: id,
                            group_id: Number($toggle.closest('tr').find('.btn-edit-purpose').data('group-id') || 0),
                            group_name: $toggle.closest('tr').find('.btn-edit-purpose').data('group-name') || rowData[1],
                            name: $toggle.closest('tr').find('.btn-edit-purpose').data('name') || rowData[2],
                            description: $toggle.closest('tr').find('.btn-edit-purpose').data('description') || '',
                            is_active: res.new_status
                        };

                        rowData[4] = formatPurposeStatus(purpose.is_active);
                        rowData[5] = formatPurposeToggle(purpose);
                        rowData[6] = formatPurposeActions(purpose);
                        row.data(rowData).draw(false);
                    }

                    toast.fire({ icon: 'success', title: res.message });
                } else {
                    $toggle.prop('checked', !$toggle.prop('checked'));
                    toast.fire({ icon: 'error', title: res.message });
                }
            },
            error: function () {
                $toggle.prop('checked', !$toggle.prop('checked'));
                toast.fire({ icon: 'error', title: 'An error occurred.' });
            }
        });
    });

});
</script>
<?= $this->endSection() ?>
