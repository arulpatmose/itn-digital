<?php
/**
 * Time Slots — inline AJAX CRUD.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Time Slots</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('time_slot.create')): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-slot">
                                Add New
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-time-slots">
                            <thead>
                                <tr>
                                    <th class="rowIndex text-center noOrder">#</th>
                                    <th class="slotLabel">Label</th>
                                    <th class="startTime">Start Time</th>
                                    <th class="endTime">End Time</th>
                                    <th class="tableAction all text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($time_slots as $i => $slot): ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td><?= esc($slot['label']) ?></td>
                                        <td><?= esc(substr($slot['start_time'], 0, 5)) ?></td>
                                        <td><?= esc(substr($slot['end_time'], 0, 5)) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if (auth()->user()->can('time_slot.edit')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success btn-edit-slot"
                                                        data-id="<?= $slot['id'] ?>"
                                                        data-label="<?= esc($slot['label']) ?>"
                                                        data-start="<?= esc($slot['start_time']) ?>"
                                                        data-end="<?= esc($slot['end_time']) ?>"
                                                        data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (auth()->user()->can('time_slot.delete')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete-slot"
                                                        data-id="<?= $slot['id'] ?>"
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
<div class="modal fade" id="modal-add-slot" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Time Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="add-slot-label" placeholder="e.g. Morning Session">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="add-slot-start">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="add-slot-end">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-save-slot">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modal-edit-slot" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Time Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-slot-id">
                <div class="mb-3">
                    <label class="form-label">Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-slot-label">
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="edit-slot-start">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="edit-slot-end">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-update-slot">Update</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
$(function () {
    var canEditSlot = <?= json_encode(auth()->user()->can('time_slot.edit')) ?>;
    var canDeleteSlot = <?= json_encode(auth()->user()->can('time_slot.delete')) ?>;
    var slotTable = null;

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function normalizeTime(value) {
        return (value || '').substring(0, 5);
    }

    function formatSlotActions(slot) {
        var buttons = '<div class="btn-group">';

        if (canEditSlot) {
            buttons += '<button type="button" class="btn btn-sm btn-success btn-edit-slot"'
                + ' data-id="' + slot.id + '"'
                + ' data-label="' + escapeHtml(slot.label) + '"'
                + ' data-start="' + escapeHtml(slot.start_time) + '"'
                + ' data-end="' + escapeHtml(slot.end_time) + '"'
                + ' data-bs-toggle="tooltip" title="Edit">'
                + '<i class="fa fa-pencil-alt"></i></button>';
        }

        if (canDeleteSlot) {
            buttons += '<button type="button" class="btn btn-sm btn-danger btn-delete-slot"'
                + ' data-id="' + slot.id + '"'
                + ' data-bs-toggle="tooltip" title="Delete">'
                + '<i class="fa fa-times"></i></button>';
        }

        buttons += '</div>';
        return buttons;
    }

    if ($('#table-time-slots').length) {
        slotTable = $('#table-time-slots').DataTable({
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
            autoWidth: false,
            responsive: true,
            order: [[2, 'asc']],
            stateSave: true,
            info: true,
            columnDefs: [
                { targets: 'rowIndex', width: '5%', className: 'text-center', orderable: false },
                { targets: 'slotLabel', width: '35%' },
                { targets: 'startTime', width: '20%' },
                { targets: 'endTime', width: '20%' },
                { targets: 'tableAction', width: '20%', className: 'text-center', orderable: false }
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

    // Add slot
    $('#btn-save-slot').on('click', function () {
        var label = $('#add-slot-label').val().trim();
        var start = $('#add-slot-start').val();
        var end   = $('#add-slot-end').val();

        if (!label || !start || !end) { toast.fire({ icon: 'warning', title: 'All fields are required.' }); return; }

        $.ajax({
            url: _AppUri + '/time-slots/submit',
            type: 'POST',
            data: { label: label, start_time: start, end_time: end },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modal-add-slot').modal('hide');
                    $('#add-slot-label').val('');
                    $('#add-slot-start').val('');
                    $('#add-slot-end').val('');

                    if (slotTable && res.data) {
                        slotTable.row.add([
                            '',
                            escapeHtml(res.data.label),
                            escapeHtml(normalizeTime(res.data.start_time)),
                            escapeHtml(normalizeTime(res.data.end_time)),
                            formatSlotActions(res.data)
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
    $(document).on('click', '.btn-edit-slot', function () {
        $('#edit-slot-id').val($(this).data('id'));
        $('#edit-slot-label').val($(this).data('label'));
        $('#edit-slot-start').val($(this).data('start').substring(0, 5));
        $('#edit-slot-end').val($(this).data('end').substring(0, 5));
        $('#modal-edit-slot').modal('show');
    });

    // Update slot
    $('#btn-update-slot').on('click', function () {
        var id    = $('#edit-slot-id').val();
        var label = $('#edit-slot-label').val().trim();
        var start = $('#edit-slot-start').val();
        var end   = $('#edit-slot-end').val();
        var $button = $('.btn-edit-slot[data-id="' + id + '"]').first();

        if (!label || !start || !end) { toast.fire({ icon: 'warning', title: 'All fields are required.' }); return; }

        $.ajax({
            url: _AppUri + '/time-slots/update/' + id,
            type: 'POST',
            data: { label: label, start_time: start, end_time: end },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modal-edit-slot').modal('hide');
                    if (slotTable && $button.length) {
                        var updatedSlot = {
                            id: id,
                            label: label,
                            start_time: start,
                            end_time: end
                        };

                        slotTable.row($button.closest('tr')).data([
                            '',
                            escapeHtml(updatedSlot.label),
                            escapeHtml(normalizeTime(updatedSlot.start_time)),
                            escapeHtml(normalizeTime(updatedSlot.end_time)),
                            formatSlotActions(updatedSlot)
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

    // Delete slot
    $(document).on('click', '.btn-delete-slot', function () {
        var id = $(this).data('id');
        toast.fire({
            title: 'Delete this time slot?',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#dc3545',
        }).then(function (result) {
            if (result.value) {
                var $button = $('.btn-delete-slot[data-id="' + id + '"]').first();
                $.ajax({
                    url: _AppUri + '/time-slots/delete',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            if (slotTable && $button.length) {
                                slotTable.row($button.closest('tr')).remove().draw(false);
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
