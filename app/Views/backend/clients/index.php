<?php

/**
 * view_clients
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
                        List of Clients
                    </h3>
                    <div class="block-options">
                        <a href="<?php echo base_url('clients/add'); ?>" role="button" class="btn btn-sm btn-primary">Add
                            New</a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-clients">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Name</th>
                                    <th>Address</th>
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
    // Datatable for Clients
    jQuery(document).ready(function() {
        if (jQuery('#table-clients').length) {
            jQuery('#table-clients').DataTable({
                ajax: {
                    url: '/api/get-all-clients',
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
                        data: 'client_id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'address'
                    },
                    {
                        data: 'client_id'
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
                                '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/clients/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Client" data-bs-title="Edit Client" data-bs-placement="left">' +
                                '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                                '<a role="button" class="btn btn-sm btn-danger" id="delete-client-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/clients/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Client" data-bs-title="Remove Client" data-bs-placement="right">' +
                                '<i class="fa fa-fw fa-times"></i>' + '</a>' + ' </div>';
                        }
                    }
                ]
            });
        }
    });
</script>
<?= $this->endSection() ?>