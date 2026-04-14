<?php

/**
 * view_users
 *
 * Author: Arul Patmose
 *
 * Load Default Global Template and Extend
 *
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        List of Users
                    </h3>
                    <div class="block-options">
                        <a href="<?= base_url('users/add') ?>" role="button" class="btn btn-sm btn-primary">Add New</a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-users">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Groups</th>
                                    <th>Verified</th>
                                    <th>Status</th>
                                    <th>Last Active</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
    // Permission flags from server
    var _canEdit    = <?= json_encode($can_edit) ?>;
    var _canDelete  = <?= json_encode($can_delete) ?>;
    var _canRestore = <?= json_encode($can_restore) ?>;
    var _canBan     = <?= json_encode($can_ban) ?>;
    var _canUnban   = <?= json_encode($can_unban) ?>;

    // Datatable for Users
    jQuery(document).ready(function () {
        if (jQuery('#table-users').length) {
            jQuery('#table-users').DataTable({
                ajax: {
                    url: '/api/get-all-users',
                    data: function (data) { return { data: data }; },
                    dataSrc: function (data) { return data.aaData; },
                    method: 'post'
                },
                serverSide: true,
                processing: true,
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [[5, 10, 15, 20, 50], [5, 10, 15, 20, 50]],
                autoWidth: false,
                responsive: true,
                stateSave: true,
                info: true,
                columns: [
                    { data: 'id' },
                    { data: 'first_name' },
                    { data: 'last_name' },
                    { data: 'email' },
                    { data: 'groups' },
                    { data: 'email_verified' },
                    { data: 'status' },
                    { data: 'last_active' },
                    { data: 'id' }
                ],
                columnDefs: [
                    {
                        targets: [0],
                        width: '4%',
                        orderable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        targets: [4],
                        orderable: false,
                        render: function (data, type, row, meta) {
                            if (data && data.trim() !== '') {
                                var _groups = data.split(',');
                                return $.map(_groups, function (value) {
                                    return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success me-1">' + value.trim() + '</span>';
                                }).join('');
                            }
                            return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">N/A</span>';
                        }
                    },
                    {
                        targets: [5],
                        width: '7%',
                        orderable: false,
                        render: function (data, type, row, meta) {
                            if (row.status === 'deleted') {
                                return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-secondary-light text-secondary">N/A</span>';
                            }
                            return data
                                ? '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Yes</span>'
                                : '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">No</span>';
                        }
                    },
                    {
                        targets: [6],
                        width: '10%',
                        render: function (data, type, row, meta) {
                            if (data === 'deleted') {
                                return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-danger-light text-danger">Deleted</span>';
                            }
                            if (data === 'banned') {
                                var msg = row.status_message ? ' <small class="text-muted">(' + row.status_message + ')</small>' : '';
                                return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">Banned</span>' + msg;
                            }
                            return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Active</span>';
                        }
                    },
                    {
                        targets: [8],
                        width: '12%',
                        orderable: false,
                        render: function (data, type, row, meta) {
                            var uri     = URI().origin();
                            var buttons = '<div class="btn-group">';

                            if (row.status === 'deleted') {
                                // Deleted user — restore only
                                if (_canRestore) {
                                    buttons += '<button type="button" class="btn btn-sm btn-secondary js-restore-user" data-id="' + data + '" data-url="' + _AppUri + '/users/restore" data-bs-toggle="tooltip" title="Restore User"><i class="fa fa-fw fa-undo"></i></button>';
                                }
                            } else if (row.status === 'banned') {
                                // Banned user — edit + unban
                                if (_canEdit) {
                                    buttons += '<a role="button" class="btn btn-sm btn-success" href="' + uri + '/users/edit/' + data + '" data-bs-toggle="tooltip" title="Edit User"><i class="fa fa-fw fa-pencil-alt"></i></a>';
                                }
                                if (_canUnban) {
                                    buttons += '<button type="button" class="btn btn-sm btn-warning js-unban-user" data-id="' + data + '" data-url="' + _AppUri + '/users/unban" data-bs-toggle="tooltip" title="Unban User"><i class="fa fa-fw fa-unlock"></i></button>';
                                }
                            } else {
                                // Active user — edit + ban + delete
                                if (_canEdit) {
                                    buttons += '<a role="button" class="btn btn-sm btn-success" href="' + uri + '/users/edit/' + data + '" data-bs-toggle="tooltip" title="Edit User"><i class="fa fa-fw fa-pencil-alt"></i></a>';
                                }
                                if (_canBan) {
                                    buttons += '<button type="button" class="btn btn-sm btn-warning js-ban-user" data-id="' + data + '" data-url="' + _AppUri + '/users/ban" data-bs-toggle="tooltip" title="Ban User"><i class="fa fa-fw fa-ban"></i></button>';
                                }
                                if (_canDelete) {
                                    buttons += '<button type="button" class="btn btn-sm btn-danger js-delete-user" data-id="' + data + '" data-url="' + _AppUri + '/users/delete" data-bs-toggle="tooltip" title="Delete User"><i class="fa fa-fw fa-times"></i></button>';
                                }
                            }

                            buttons += '</div>';
                            return buttons;
                        }
                    }
                ]
            });
        }
    });

    // ── Delete User ──────────────────────────────────────────────────────────
    jQuery(document).on('click', '.js-delete-user', function (e) {
        e.preventDefault();
        var id  = $(this).data('id');
        var url = $(this).data('url');

        toast.fire({
            title: 'Are you sure?',
            html: 'This user will be soft-deleted and can be restored later.',
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonText: 'Yes, delete',
            allowOutsideClick: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: url, type: 'POST', dataType: 'json',
                    data: { user_id: id },
                    success: function (r) {
                        var icon = r.success ? 'success' : 'error';
                        toast.fire({ title: icon === 'success' ? 'Deleted' : 'Error', icon: icon, html: r.message })
                            .then(function () { if (r.success) jQuery('#table-users').DataTable().ajax.reload(null, false); });
                    },
                    error: function () { toast.fire({ title: 'Error', icon: 'error', html: 'An unexpected error occurred.' }); }
                });
            }
        });
    });

    // ── Ban User ─────────────────────────────────────────────────────────────
    jQuery(document).on('click', '.js-ban-user', function (e) {
        e.preventDefault();
        var id  = $(this).data('id');
        var url = $(this).data('url');

        Swal.fire({
            title: 'Ban this user?',
            html: '<input id="ban-reason" class="swal2-input" placeholder="Reason for ban (optional)">',
            showCancelButton: true,
            confirmButtonText: 'Ban User',
            confirmButtonColor: '#f59e0b',
            allowOutsideClick: false,
            preConfirm: function () {
                return document.getElementById('ban-reason').value;
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: url, type: 'POST', dataType: 'json',
                    data: { user_id: id, reason: result.value || 'No reason provided.' },
                    success: function (r) {
                        toast.fire({ title: r.status === 'success' ? 'Banned' : 'Error', icon: r.status, html: r.message })
                            .then(function () { if (r.status === 'success') jQuery('#table-users').DataTable().ajax.reload(null, false); });
                    },
                    error: function () { toast.fire({ title: 'Error', icon: 'error', html: 'An unexpected error occurred.' }); }
                });
            }
        });
    });

    // ── Unban User ───────────────────────────────────────────────────────────
    jQuery(document).on('click', '.js-unban-user', function (e) {
        e.preventDefault();
        var id  = $(this).data('id');
        var url = $(this).data('url');

        toast.fire({
            title: 'Unban this user?',
            html: 'Their account will be restored to active.',
            showCancelButton: true,
            confirmButtonText: 'Yes, unban',
            allowOutsideClick: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: url, type: 'POST', dataType: 'json',
                    data: { user_id: id },
                    success: function (r) {
                        toast.fire({ title: r.status === 'success' ? 'Unbanned' : 'Error', icon: r.status, html: r.message })
                            .then(function () { if (r.status === 'success') jQuery('#table-users').DataTable().ajax.reload(null, false); });
                    },
                    error: function () { toast.fire({ title: 'Error', icon: 'error', html: 'An unexpected error occurred.' }); }
                });
            }
        });
    });

    // ── Restore User ─────────────────────────────────────────────────────────
    jQuery(document).on('click', '.js-restore-user', function (e) {
        e.preventDefault();
        var id  = $(this).data('id');
        var url = $(this).data('url');

        toast.fire({
            title: 'Restore this user?',
            html: 'Their account will be reactivated.',
            showCancelButton: true,
            confirmButtonText: 'Yes, restore',
            allowOutsideClick: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: url, type: 'POST', dataType: 'json',
                    data: { user_id: id },
                    success: function (r) {
                        toast.fire({ title: r.status === 'success' ? 'Restored' : 'Error', icon: r.status, html: r.message })
                            .then(function () { if (r.status === 'success') jQuery('#table-users').DataTable().ajax.reload(null, false); });
                    },
                    error: function () { toast.fire({ title: 'Error', icon: 'error', html: 'An unexpected error occurred.' }); }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>
