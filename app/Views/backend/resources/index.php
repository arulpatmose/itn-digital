<?php
/**
 * Resources list.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Resources</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('resource.create')): ?>
                            <a href="<?= base_url('resources/add') ?>" class="btn btn-sm btn-primary">Add New</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-resources">
                            <thead>
                                <tr>
                                    <th class="rowIndex text-center noOrder">#</th>
                                    <th class="resourceName">Name</th>
                                    <th class="resourceType">Type</th>
                                    <th class="resourceDescription">Description</th>
                                    <th class="availability text-center">Availability</th>
                                    <th class="statusToggle text-center noOrder">Status Toggle</th>
                                    <th class="tableAction all text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resources as $i => $r): ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td><?= esc($r['name']) ?></td>
                                        <td><?= esc($r['type_name'] ?? '—') ?></td>
                                        <td class="text-muted small"><?= esc($r['description'] ?? '—') ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= (int) $r['status'] === 1 ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= (int) $r['status'] === 1 ? 'Available' : 'Unavailable' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (auth()->user()->can('resource.edit')): ?>
                                                <div class="form-check form-switch d-inline-flex justify-content-center mb-0">
                                                    <input class="form-check-input resource-status-toggle" type="checkbox" role="switch"
                                                        data-id="<?= $r['id'] ?>"
                                                        <?= (int) $r['status'] === 1 ? 'checked' : '' ?>
                                                        aria-label="<?= (int) $r['status'] === 1 ? 'Available' : 'Unavailable' ?>">
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if (auth()->user()->can('resource.edit')): ?>
                                                    <a href="<?= base_url('resources/edit/' . $r['id']) ?>"
                                                        class="btn btn-sm btn-success"
                                                        data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (auth()->user()->can('resource.delete')): ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-delete-resource"
                                                        data-id="<?= $r['id'] ?>"
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
    var canEditResource = <?= json_encode(auth()->user()->can('resource.edit')) ?>;
    var canDeleteResource = <?= json_encode(auth()->user()->can('resource.delete')) ?>;
    var resourceTable = null;

    function formatAvailabilityBadge(status) {
        return '<span class="badge ' + (Number(status) === 1 ? 'bg-success' : 'bg-secondary') + '">'
            + (Number(status) === 1 ? 'Available' : 'Unavailable')
            + '</span>';
    }

    function formatToggleButton(resource) {
        if (!canEditResource) {
            return '<span class="text-muted small">—</span>';
        }

        return '<div class="form-check form-switch d-inline-flex justify-content-center mb-0">'
            + '<input class="form-check-input resource-status-toggle" type="checkbox" role="switch"'
            + ' data-id="' + resource.id + '"'
            + (Number(resource.status) === 1 ? ' checked' : '')
            + ' aria-label="' + (Number(resource.status) === 1 ? 'Available' : 'Unavailable') + '">'
            + '</div>';
    }

    function formatResourceActions(resource) {
        var buttons = '<div class="btn-group">';

        if (canEditResource) {
            buttons += '<a href="' + _AppUri + '/resources/edit/' + resource.id + '"'
                + ' class="btn btn-sm btn-success"'
                + ' data-bs-toggle="tooltip" title="Edit">'
                + '<i class="fa fa-pencil-alt"></i></a>';
        }

        if (canDeleteResource) {
            buttons += '<button type="button"'
                + ' class="btn btn-sm btn-danger btn-delete-resource"'
                + ' data-id="' + resource.id + '"'
                + ' data-bs-toggle="tooltip" title="Delete">'
                + '<i class="fa fa-times"></i></button>';
        }

        buttons += '</div>';
        return buttons;
    }

    // Flash messages
    <?php if ($flash = session()->getFlashdata('success')): ?>
    Swal.fire({ icon: 'success', title: 'Success', text: <?= json_encode($flash) ?>, confirmButtonColor: '#28a745' });
    <?php endif; ?>
    <?php if ($flash = session()->getFlashdata('error')): ?>
    Swal.fire({ icon: 'error', title: 'Error', text: <?= json_encode($flash) ?>, confirmButtonColor: '#dc3545' });
    <?php endif; ?>

    if ($('#table-resources').length) {
        resourceTable = $('#table-resources').DataTable({
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
                { targets: 'resourceName', width: '20%' },
                { targets: 'resourceType', width: '15%' },
                { targets: 'resourceDescription', width: '30%' },
                { targets: 'availability', width: '12%', className: 'text-center' },
                { targets: 'statusToggle', width: '8%', className: 'text-center', orderable: false },
                { targets: 'tableAction', width: '10%', className: 'text-center', orderable: false }
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

    // Toggle status
    $(document).on('change', '.resource-status-toggle', function () {
        var id = $(this).data('id');
        var $toggle = $(this);
        $.ajax({
            url: _AppUri + '/resources/toggle-status',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    if (resourceTable && $toggle.length) {
                        var row = resourceTable.row($toggle.closest('tr'));
                        var rowData = row.data();
                        var resource = {
                            id: id,
                            status: res.new_status
                        };

                        rowData[4] = formatAvailabilityBadge(resource.status);
                        rowData[5] = formatToggleButton(resource);
                        rowData[6] = formatResourceActions(resource);
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

    // Delete resource
    $(document).on('click', '.btn-delete-resource', function () {
        var id = $(this).data('id');
        toast.fire({
            title: 'Delete this resource?',
            html: 'This cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#dc3545',
        }).then(function (result) {
            if (result.value) {
                var $button = $('.btn-delete-resource[data-id="' + id + '"]').first();
                $.ajax({
                    url: _AppUri + '/resources/delete',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            if (resourceTable && $button.length) {
                                resourceTable.row($button.closest('tr')).remove().draw(false);
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
