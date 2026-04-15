<?php
/**
 * Booking Purpose Groups — inline AJAX CRUD.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Booking Purpose Groups</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('bookingpurposegroup.create')): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-group">
                                Add New
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-purpose-groups">
                            <thead>
                                <tr>
                                    <th class="rowIndex text-center noOrder">#</th>
                                    <th class="groupName">Name</th>
                                    <th class="groupSlug">Slug</th>
                                    <th class="groupDescription">Description</th>
                                    <th class="groupSortOrder text-center">Order</th>
                                    <th class="statusLabel text-center">Status</th>
                                    <th class="statusToggle text-center noOrder">Status Toggle</th>
                                    <th class="tableAction all text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purpose_groups as $i => $group): ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td><?= esc($group['name']) ?></td>
                                        <td><code><?= esc($group['slug']) ?></code></td>
                                        <td class="text-muted small"><?= esc($group['description'] ?? '—') ?></td>
                                        <td class="text-center"><?= (int) $group['sort_order'] ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= (int) $group['is_active'] === 1 ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= (int) $group['is_active'] === 1 ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (auth()->user()->can('bookingpurposegroup.edit')): ?>
                                                <div class="form-check form-switch d-inline-flex justify-content-center mb-0">
                                                    <input class="form-check-input group-status-toggle" type="checkbox" role="switch"
                                                        data-id="<?= $group['id'] ?>"
                                                        <?= (int) $group['is_active'] === 1 ? 'checked' : '' ?>
                                                        aria-label="<?= (int) $group['is_active'] === 1 ? 'Active' : 'Inactive' ?>">
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if (auth()->user()->can('bookingpurposegroup.edit')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success btn-edit-group"
                                                        data-id="<?= $group['id'] ?>"
                                                        data-name="<?= esc($group['name']) ?>"
                                                        data-description="<?= esc($group['description'] ?? '') ?>"
                                                        data-sort-order="<?= (int) $group['sort_order'] ?>"
                                                        data-active="<?= (int) $group['is_active'] ?>"
                                                        data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (auth()->user()->can('bookingpurposegroup.delete')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete-group"
                                                        data-id="<?= $group['id'] ?>"
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

<!-- Add Modal -->
<div class="modal fade" id="modal-add-group" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Purpose Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="add-group-name" placeholder="e.g. Production">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" id="add-group-description" rows="2" placeholder="Brief description of the group"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order <small class="text-muted">(lower = higher in list)</small></label>
                    <input type="number" class="form-control" id="add-group-sort-order" value="0" min="0">
                </div>
                <div class="mb-0">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="add-group-active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-save-group">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modal-edit-group" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Purpose Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-group-id">
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-group-name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" id="edit-group-description" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order <small class="text-muted">(lower = higher in list)</small></label>
                    <input type="number" class="form-control" id="edit-group-sort-order" min="0">
                </div>
                <div class="mb-0">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="edit-group-active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-update-group">Update</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
$(function () {
    var canEdit   = <?= json_encode(auth()->user()->can('bookingpurposegroup.edit')) ?>;
    var canDelete = <?= json_encode(auth()->user()->can('bookingpurposegroup.delete')) ?>;
    var groupTable = null;

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function formatStatus(isActive) {
        return '<span class="badge ' + (Number(isActive) === 1 ? 'bg-success' : 'bg-secondary') + '">'
            + (Number(isActive) === 1 ? 'Active' : 'Inactive') + '</span>';
    }

    function formatToggle(group) {
        if (!canEdit) return '<span class="text-muted small">—</span>';
        return '<div class="form-check form-switch d-inline-flex justify-content-center mb-0">'
            + '<input class="form-check-input group-status-toggle" type="checkbox" role="switch"'
            + ' data-id="' + group.id + '"'
            + (Number(group.is_active) === 1 ? ' checked' : '')
            + ' aria-label="' + (Number(group.is_active) === 1 ? 'Active' : 'Inactive') + '">'
            + '</div>';
    }

    function formatActions(group) {
        var buttons = '<div class="btn-group">';
        if (canEdit) {
            buttons += '<button type="button" class="btn btn-sm btn-success btn-edit-group"'
                + ' data-id="' + group.id + '"'
                + ' data-name="' + escapeHtml(group.name) + '"'
                + ' data-description="' + escapeHtml(group.description || '') + '"'
                + ' data-sort-order="' + Number(group.sort_order || 0) + '"'
                + ' data-active="' + Number(group.is_active) + '"'
                + ' data-bs-toggle="tooltip" title="Edit">'
                + '<i class="fa fa-pencil-alt"></i></button>';
        }
        if (canDelete) {
            buttons += '<button type="button" class="btn btn-sm btn-danger btn-delete-group"'
                + ' data-id="' + group.id + '"'
                + ' data-bs-toggle="tooltip" title="Delete">'
                + '<i class="fa fa-times"></i></button>';
        }
        return buttons + '</div>';
    }

    if ($('#table-purpose-groups').length) {
        groupTable = $('#table-purpose-groups').DataTable({
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            stateSave: true,
            info: true,
            order: [[4, 'asc'], [1, 'asc']],
            columnDefs: [
                { targets: 'rowIndex',        width: '5%',  className: 'text-center', orderable: false },
                { targets: 'groupName',        width: '18%' },
                { targets: 'groupSlug',        width: '16%' },
                { targets: 'groupDescription', width: '24%' },
                { targets: 'groupSortOrder',   width: '8%',  className: 'text-center' },
                { targets: 'statusLabel',      width: '9%',  className: 'text-center' },
                { targets: 'statusToggle',     width: '10%', className: 'text-center', orderable: false },
                { targets: 'tableAction',      width: '12%', className: 'text-center', orderable: false }
            ],
            drawCallback: function () {
                var api   = this.api();
                var start = api.page.info().start;
                api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                    cell.innerHTML = start + i + 1;
                });
            }
        });
    }

    // ── Add ──────────────────────────────────────────────────────────────────
    $('#btn-save-group').on('click', function () {
        var name      = $('#add-group-name').val().trim();
        var desc      = $('#add-group-description').val().trim();
        var sortOrder = $('#add-group-sort-order').val();
        var active    = $('#add-group-active').val();

        if (!name) { toast.fire({ icon: 'warning', title: 'Group name is required.' }); return; }

        $.ajax({
            url:      _AppUri + '/booking-purpose-groups/submit',
            type:     'POST',
            data:     { name: name, description: desc, sort_order: sortOrder, is_active: active },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modal-add-group').modal('hide');
                    $('#add-group-name').val('');
                    $('#add-group-description').val('');
                    $('#add-group-sort-order').val('0');
                    $('#add-group-active').val('1');

                    if (groupTable && res.data) {
                        groupTable.row.add([
                            '',
                            escapeHtml(res.data.name),
                            '<code>' + escapeHtml(res.data.slug) + '</code>',
                            '<span class="text-muted small">' + escapeHtml(res.data.description || '—') + '</span>',
                            Number(res.data.sort_order || 0),
                            formatStatus(res.data.is_active),
                            formatToggle(res.data),
                            formatActions(res.data)
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

    // ── Edit ─────────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-edit-group', function () {
        $('#edit-group-id').val($(this).data('id'));
        $('#edit-group-name').val($(this).data('name'));
        $('#edit-group-description').val($(this).data('description'));
        $('#edit-group-sort-order').val($(this).data('sort-order'));
        $('#edit-group-active').val(String($(this).data('active')));
        $('#modal-edit-group').modal('show');
    });

    $('#btn-update-group').on('click', function () {
        var id        = $('#edit-group-id').val();
        var name      = $('#edit-group-name').val().trim();
        var desc      = $('#edit-group-description').val().trim();
        var sortOrder = $('#edit-group-sort-order').val();
        var active    = $('#edit-group-active').val();
        var $button   = $('.btn-edit-group[data-id="' + id + '"]').first();

        if (!name) { toast.fire({ icon: 'warning', title: 'Group name is required.' }); return; }

        $.ajax({
            url:      _AppUri + '/booking-purpose-groups/update/' + id,
            type:     'POST',
            data:     { name: name, description: desc, sort_order: sortOrder, is_active: active },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modal-edit-group').modal('hide');
                    if (groupTable && $button.length) {
                        var g = res.data;
                        groupTable.row($button.closest('tr')).data([
                            '',
                            escapeHtml(g.name),
                            '<code>' + escapeHtml(g.slug) + '</code>',
                            '<span class="text-muted small">' + escapeHtml(g.description || '—') + '</span>',
                            Number(g.sort_order || 0),
                            formatStatus(g.is_active),
                            formatToggle(g),
                            formatActions(g)
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

    // ── Delete ───────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-delete-group', function () {
        var id = $(this).data('id');
        toast.fire({
            title: 'Delete this purpose group?',
            text: 'Groups with assigned purposes cannot be deleted.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#dc3545',
        }).then(function (result) {
            if (result.value) {
                var $button = $('.btn-delete-group[data-id="' + id + '"]').first();
                $.ajax({
                    url:      _AppUri + '/booking-purpose-groups/delete',
                    type:     'POST',
                    data:     { id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            if (groupTable && $button.length) {
                                groupTable.row($button.closest('tr')).remove().draw(false);
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

    // ── Toggle Status ────────────────────────────────────────────────────────
    $(document).on('change', '.group-status-toggle', function () {
        var id      = $(this).data('id');
        var $toggle = $(this);

        $.ajax({
            url:      _AppUri + '/booking-purpose-groups/toggle-status',
            type:     'POST',
            data:     { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    if (groupTable && $toggle.length) {
                        var row     = groupTable.row($toggle.closest('tr'));
                        var rowData = row.data();
                        var $editBtn = $toggle.closest('tr').find('.btn-edit-group');
                        var g = {
                            id:          id,
                            name:        $editBtn.data('name') || rowData[1],
                            slug:        rowData[2],
                            description: $editBtn.data('description') || '',
                            sort_order:  $editBtn.data('sort-order') || rowData[4],
                            is_active:   res.new_status
                        };
                        rowData[5] = formatStatus(g.is_active);
                        rowData[6] = formatToggle(g);
                        rowData[7] = formatActions(g);
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
