<?php

/**
 * view_programs
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
                        List of Programs
                    </h3>
                    <div class="block-options">
                        <a href="<?php echo base_url('programs/add'); ?>" role="button" class="btn btn-sm btn-primary">Add
                            New</a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-programs">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Thumbnail</th>
                                    <th>Name</th>
                                    <th>Type</th>
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
    // Datatable for Programs
    jQuery(document).ready(function() {
        if (jQuery('#table-programs').length) {
            jQuery('#table-programs').DataTable({
                ajax: {
                    url: '/api/get-all-programs',
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
                        data: 'prog_id'
                    },
                    {
                        data: 'thumbnail'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'prog_id'
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
                        targets: [1],
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row, meta) {
                            var thumbnail;
                            var uri = URI().origin();

                            if (data == null || data == 'NULL') {
                                thumbnail = 'No-Image-Placeholder.svg';
                            } else {
                                thumbnail = data;
                            }

                            return '<img class="w-50 mx-auto d-block rounded" src="' + uri + '/uploads/thumbnails/' + thumbnail + '">';
                        },
                    },
                    {
                        targets: [3],
                        render: function(data, type, row, meta) {
                            var type;
                            switch (data) {
                                case "0":
                                    type = "Teledrama";
                                    break;
                                case "1":
                                    type = "TV Show";
                                    break;
                                case "2":
                                    type = "Other";
                                    break;
                                default:
                                    type = "N/A";
                            }
                            return type;
                        }
                    },
                    {
                        targets: [4],
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row, meta) {
                            var uri = URI().origin();

                            return '<div class="btn-group">' +
                                '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/programs/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Program" data-bs-title="Edit Program" data-bs-placement="left">' +
                                '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                                '<a role="button" class="btn btn-sm btn-danger" id="delete-program-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/programs/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Program" data-bs-title="Remove Program" data-bs-placement="right">' +
                                '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                        }
                    }
                ]
            });
        }
    });

    // Delete Program Function
    jQuery(document).on('click', '#delete-program-button', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var url = $(this).data('url');
        toast.fire({
            title: "Are you sure?",
            html: "You won't be able to revert this!",
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonText: "Yes, Delete it",
            allowOutsideClick: false
        }).then(function(result) {
            if (result.value) {
                $.post(url, {
                    id: id
                }, function(data) {
                    response = jQuery.parseJSON(data);
                    if (response.code == 1) {
                        toast.fire({
                            title: "Success",
                            icon: 'success',
                            html: response.message
                        }).then(function() {
                            jQuery('#table-programs').DataTable().ajax.reload(null, false);
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