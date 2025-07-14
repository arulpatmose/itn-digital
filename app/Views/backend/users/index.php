<?php

/**
 * view_spots
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
                        <a href="<?php echo base_url('users/add'); ?>" role="button" class="btn btn-sm btn-primary">Add
                            New</a>
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
                                    <th>Groups</th>
                                    <th>Last Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

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
    // Datatable for Users
    jQuery(document).ready(function() {
        if (jQuery('#table-users').length) {
            jQuery('#table-users').DataTable({
                ajax: {
                    url: '/api/get-all-users',
                    data: function(data) {
                        return {
                            data: data
                        }
                    },
                    dataSrc: function(data) {
                        return data.aaData;
                    },
                    method: 'post'
                },
                serverSide: true,
                processing: true,
                pagingType: "full_numbers",
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 15, 20, 50],
                    [5, 10, 15, 20, 50]
                ],
                autoWidth: false,
                responsive: true,
                stateSave: true,
                info: true,
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'first_name'
                    },
                    {
                        data: 'last_name'
                    },
                    {
                        data: 'groups'
                    },
                    {
                        data: 'last_active'
                    },
                    {
                        data: 'id'
                    }
                ],
                columnDefs: [{
                        targets: [0],
                        width: "5%",
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        targets: [3],
                        render: function(data, type, row, meta) {
                            if (data.trim() !== '') {
                                var _groups = data.split(',');
                                // Loop through the array and create chip elements
                                // Create a list of spans and join them into a single string
                                var groupList = $.map(_groups, function(value) {
                                    return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success me-1">' + value + '</span>';
                                }).join('');

                                return groupList;
                            } else {
                                return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">N/A</span>';
                            }
                        }
                    },
                    {
                        targets: [5],
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row, meta) {
                            var uri = URI().origin();

                            return '<div class="btn-group">' +
                                '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/users/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit User" data-bs-title="Edit User" data-bs-placement="left">' +
                                '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                                '<a role="button" class="btn btn-sm btn-danger" id="delete-user-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/users/delete' + '" data-bs-toggle="tooltip" aria-label="Remove User" data-bs-title="Remove User" data-bs-placement="right">' +
                                '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                        }
                    }
                ]
            });
        }
    });

    // Delete User Function
    jQuery(document).on('click', '#delete-user-button', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var url = $(this).data('url');
        toast.fire({
            title: "Are you sure?",
            html: "You won't be able to revert this!",
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonText: "Yes, delete it",
            allowOutsideClick: false
        }).then(function(result) {
            if (result.value) {
                $.post(url, {
                    user_id: id
                }, function(data) {
                    response = jQuery.parseJSON(data);
                    if (response.code == 1) {
                        toast.fire({
                            title: "Success",
                            icon: 'success',
                            html: response.message
                        }).then(function() {
                            jQuery('#table-users').DataTable().ajax.reload(null, false);
                        });
                    } else {
                        toast.fire({
                            title: "Error",
                            icon: 'error',
                            html: response.message
                        });
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>