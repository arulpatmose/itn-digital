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
                        List of Spots
                    </h3>
                    <div class="block-options">
                        <a href="<?php echo base_url('spots/add'); ?>" role="button" class="btn btn-sm btn-primary">Add
                            New</a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-spots">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Name</th>
                                    <th>Priority</th>
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
    // Datatable for Spots
    jQuery(document).ready(function() {
        if (jQuery('#table-spots').length) {
            jQuery('#table-spots').DataTable({
                ajax: {
                    url: '/api/get-all-spots',
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
                order: [
                    [2, 'asc']
                ],
                columns: [{
                        data: 'spot_id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'priority'
                    },
                    {
                        data: 'spot_id'
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
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row, meta) {
                            var uri = URI().origin();

                            return '<div class="btn-group">' +
                                '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/spots/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Spot" data-bs-title="Edit Spot" data-bs-placement="left">' +
                                '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                                '<a role="button" class="btn btn-sm btn-danger" id="delete-spot-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/spots/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Spot" data-bs-title="Remove Spot" data-bs-placement="right">' +
                                '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                        }
                    }
                ]
            });
        }
    });

    // Delete Spot Function
    jQuery(document).on('click', '#delete-spot-button', function(event) {
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
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json', // Auto parses JSON response
                    success: function(response) {
                        if (response.status === 'success') {
                            toast.fire({
                                title: "Success",
                                icon: 'success',
                                html: response.message
                            }).then(function() {
                                jQuery('#table-spots').DataTable().ajax.reload(null, false);
                            });
                        } else {
                            toast.fire({
                                title: "Error",
                                icon: 'error',
                                html: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        toast.fire({
                            title: "Error",
                            icon: 'error',
                            html: "An unexpected error occurred. Please try again."
                        });
                        console.error("AJAX error:", status, error);
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>