<?php
/**
 * Resource Types — inline AJAX CRUD.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Resource Types</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('resourcetype.create')): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-type">
                                Add New
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-resource-types">
                            <thead>
                                <tr>
                                    <th class="rowIndex text-center noOrder">#</th>
                                    <th class="typeName">Name</th>
                                    <th class="typeDescription">Description</th>
                                    <th class="tableAction all text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resource_types as $i => $type): ?>
                                    <tr id="row-type-<?= $type['id'] ?>">
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td><?= esc($type['name']) ?></td>
                                        <td class="text-muted small"><?= esc($type['description'] ?? '—') ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if (auth()->user()->can('resourcetype.edit')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success btn-edit-type"
                                                        data-id="<?= $type['id'] ?>"
                                                        data-name="<?= esc($type['name']) ?>"
                                                        data-description="<?= esc($type['description'] ?? '') ?>"
                                                        data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (auth()->user()->can('resourcetype.delete')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete-type"
                                                        data-id="<?= $type['id'] ?>"
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
<div class="modal fade" id="modal-add-type" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Resource Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="add-type-name" placeholder="e.g. Studio, Conference Room">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" id="add-type-description" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-save-type">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modal-edit-type" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Resource Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-type-id">
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-type-name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" id="edit-type-description" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-update-type">Update</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
$(function () {
    var canEditType = <?= json_encode(auth()->user()->can('resourcetype.edit')) ?>;
    var canDeleteType = <?= json_encode(auth()->user()->can('resourcetype.delete')) ?>;
    var typeTable = null;

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function formatTypeActions(type) {
        var buttons = '<div class="btn-group">';

        if (canEditType) {
            buttons += '<button type="button" class="btn btn-sm btn-success btn-edit-type"'
                + ' data-id="' + type.id + '"'
                + ' data-name="' + escapeHtml(type.name) + '"'
                + ' data-description="' + escapeHtml(type.description || '') + '"'
                + ' data-bs-toggle="tooltip" title="Edit">'
                + '<i class="fa fa-pencil-alt"></i></button>';
        }

        if (canDeleteType) {
            buttons += '<button type="button" class="btn btn-sm btn-danger btn-delete-type"'
                + ' data-id="' + type.id + '"'
                + ' data-bs-toggle="tooltip" title="Delete">'
                + '<i class="fa fa-times"></i></button>';
        }

        buttons += '</div>';
        return buttons;
    }

    if ($('#table-resource-types').length) {
        typeTable = $('#table-resource-types').DataTable({
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
                { targets: 'typeName', width: '25%' },
                { targets: 'typeDescription', width: '55%' },
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

    // Add type
    $('#btn-save-type').on('click', function () {
        var name = $('#add-type-name').val().trim();
        var desc = $('#add-type-description').val().trim();

        if (!name) { toast.fire({ icon: 'warning', title: 'Name is required.' }); return; }

        $.ajax({
            url: _AppUri + '/resource-types/submit',
            type: 'POST',
            data: { name: name, description: desc },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modal-add-type').modal('hide');
                    $('#add-type-name').val('');
                    $('#add-type-description').val('');

                    if (typeTable && res.data) {
                        typeTable.row.add([
                            '',
                            escapeHtml(res.data.name),
                            '<span class="text-muted small">' + escapeHtml(res.data.description || '—') + '</span>',
                            formatTypeActions(res.data)
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

    // Open edit modal
    $(document).on('click', '.btn-edit-type', function () {
        $('#edit-type-id').val($(this).data('id'));
        $('#edit-type-name').val($(this).data('name'));
        $('#edit-type-description').val($(this).data('description'));
        $('#modal-edit-type').modal('show');
    });

    // Update type
    $('#btn-update-type').on('click', function () {
        var id   = $('#edit-type-id').val();
        var name = $('#edit-type-name').val().trim();
        var desc = $('#edit-type-description').val().trim();
        var $button = $('.btn-edit-type[data-id="' + id + '"]').first();

        if (!name) { toast.fire({ icon: 'warning', title: 'Name is required.' }); return; }

        $.ajax({
            url: _AppUri + '/resource-types/update/' + id,
            type: 'POST',
            data: { name: name, description: desc },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modal-edit-type').modal('hide');
                    if (typeTable && $button.length) {
                        var row = typeTable.row($button.closest('tr'));
                        var updatedType = {
                            id: id,
                            name: name,
                            description: desc
                        };

                        row.data([
                            '',
                            escapeHtml(updatedType.name),
                            '<span class="text-muted small">' + escapeHtml(updatedType.description || '—') + '</span>',
                            formatTypeActions(updatedType)
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

    // Delete type
    $(document).on('click', '.btn-delete-type', function () {
        var id = $(this).data('id');
        toast.fire({
            title: 'Delete this type?',
            html: 'Resources under this type will lose their type association.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#dc3545',
        }).then(function (result) {
            if (result.value) {
                var $button = $('.btn-delete-type[data-id="' + id + '"]').first();
                $.ajax({
                    url: _AppUri + '/resource-types/delete',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            if (typeTable && $button.length) {
                                typeTable.row($button.closest('tr')).remove().draw(false);
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

});
</script>
<?= $this->endSection() ?>
