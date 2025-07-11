<?php

/**
 * view_commercials
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
                        List of Commercials
                    </h3>
                    <div class="block-options">
                        <a href="<?php echo base_url('commercials/add'); ?>" role="button" class="btn btn-sm btn-primary">Add
                            New</a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-commercials">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Commercial ID</th>
                                    <th>Name</th>
                                    <th>Duration</th>
                                    <th>Format</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Client</th>
                                    <th>Added by</th>
                                    <th>Remarks</th>
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
    // Datatable for Commercials
    jQuery(document).ready(function() {
        if (jQuery('#table-commercials').length) {
            jQuery('#table-commercials').DataTable({
                ajax: {
                    url: '/api/get-all-commercials',
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
                stateSave: false,
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'com_id'
                    },
                    {
                        data: 'ucom_id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'duration'
                    },
                    {
                        data: 'format'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'sub_category'
                    },
                    {
                        data: 'client'
                    },
                    {
                        data: 'added_by'
                    },
                    {
                        data: 'remarks'
                    },
                    {
                        data: 'com_id'
                    },

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
                        render: function(data, type, row, meta) {
                            var uri = URI().origin();

                            return '<a class="link-fx text-success" href="' + uri + '/commercials/edit/' + row.com_id + '">' + data + '</a>';
                        }
                    },
                    {
                        targets: [3],
                        render: function(data, type, row, meta) {
                            return data + ' Sec';
                        }
                    },
                    {
                        targets: [7],
                        render: function(data, type, row, meta) {
                            if (data !== 0.00 || data !== null) {
                                return data.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        targets: [10],
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row, meta) {
                            var uri = URI().origin();

                            return '<div class="btn-group">' +
                                '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/commercials/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Platform" data-bs-title="Edit Commercial" data-bs-placement="left">' +
                                '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                                '<a role="button" class="btn btn-sm btn-danger" id="delete-commercial-button" data-id="' + data + '" href="#" data-url="' + _AppUri + '/commercials/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Commercial" data-bs-title="Remove Commercial" data-bs-placement="top">' +
                                '<i class="fa fa-fw fa-times"></i>' + '</a>' +
                                '<a role="button" class="btn btn-sm btn-primary" id="commercial-link-button" data-id="' + data + '" href="#" data-url="' + row.link + '" data-bs-toggle="tooltip" aria-label="Download Commercial" data-bs-title="Download Commercial" data-bs-placement="right">' +
                                '<i class="fa fa-fw fa-link"></i>' + '</a>' + ' </div>';
                        }
                    }
                ]
            });
        }
    });
</script>
<?= $this->endSection() ?>