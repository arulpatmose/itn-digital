<?php

/**
 * view_schedule
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
                        List of Schedule Items for
                        <span class="mx-1 text-success"><?php echo $schedule['usched_id']; ?></span>
                        <span> - </span>
                        <span class="mx-1 text-success"><?php echo $commercial['name']; ?></span>
                        <span class="text-gray-dark">(<?php echo $commercial['duration']; ?> Secs)</span>
                        <span> - </span>
                        <span><?php echo $program['name']; ?></span>
                    </h3>
                    <div class="block-options">
                        <a role="button" href="#" onclick="goBack()" class="btn btn-sm btn-alt-secondary">
                            Go Back
                        </a>
                        <a role="button" href="<?php echo base_url('schedules'); ?>" class="btn btn-sm btn-danger">
                            Cancel
                        </a>
                        <a href="<?php echo base_url('schedule/add/') . $schedule['sched_id']; ?>" role="button" class="btn btn-sm btn-primary">Add
                            New</a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-schedule">
                            <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center">#</th>
                                    <th>Scheduled Date</th>
                                    <th>Commercial</th>
                                    <th>Platform</th>
                                    <th>Spot</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Updated By</th>
                                    <th>Comments</th>
                                    <th>Actions</th>
                                    <th>Status</th>
                                    <th>Ref Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedule_items as $item) { ?>
                                    <tr>
                                        <td><?php echo $item['published']; ?>
                                        </td>
                                        <td><?php echo $item['scd_id']; ?>
                                        </td>
                                        <td><?php echo $item['sched_date']; ?>
                                        </td>
                                        <td><?php echo $commercial['name'] ?>
                                            <span class="mx-1 text-gray-dark">(<?php echo $commercial['duration'] ?> Secs)</span>
                                        </td>
                                        <td><?php echo $platform['name'] . ' - ' . $platform['channel']; ?>
                                        </td>
                                        <td><?php echo $item['spot_name']; ?>
                                        </td>
                                        <td><?php echo $item['a_first_name']; ?>
                                        </td>
                                        <td><?php echo $item['created_at']; ?>
                                        </td>
                                        <td><?php echo $item['u_first_name']; ?>
                                        </td>
                                        <td><?php echo $item['remarks']; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a role="button" class="btn btn-sm btn-danger" id="delete-schedule-item-button" data-schedule="<?php echo $schedule['usched_id']; ?>" data-id="<?php echo $item['scd_id']; ?>" href="#" data-url="<?php echo site_url('schedule/delete'); ?>" data-bs-toggle="tooltip" aria-label="Remove Schedule" data-bs-title="Remove Schedule" data-bs-placement="left"><i class="fa fa-fw fa-times"></i></a>
                                                <a role="button" class="btn btn-sm btn-primary" id="reference-link-button" href="#" data-url="<?php echo $item['link']; ?>" data-bs-toggle="tooltip" aria-label="View Program" data-bs-title="View Program" data-bs-placement="right"><i class="fa fa-fw fa-link"></i></a>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            if ($item['published'] === "0") {
                                                echo "Unpublished";
                                            } elseif ($item['published'] === "1") {
                                                echo "Published";
                                            } else {
                                                echo "Invalid status value"; // Handle any other value not equal to 0 or 1
                                            } ?>
                                        <td><?php echo $item['link']; ?></td>
                                    </tr>
                                <?php } ?>
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
<script src="<?= base_url('assets/js/plugins/datatables-buttons-jszip/jszip.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins/datatables-buttons/buttons.print.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins/datatables-buttons/buttons.html5.min.js'); ?>"></script>

<script>
    // Datatable for Schedule Item
    jQuery(document).ready(function() {
        if (jQuery('#table-schedule').length) {
            jQuery('#table-schedule').DataTable({
                pagingType: "full_numbers",
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 15, 20, 50],
                    [5, 10, 15, 20, 50]
                ],
                autoWidth: false,
                responsive: true,
                stateSave: false,
                info: true,
                searching: false,
                columnDefs: [{
                        targets: [0],
                        render: function(data, type, row, meta) {
                            var published;
                            switch (row[0]) {
                                case '0':
                                    published = '<i class="fa fa-times-circle text-danger"></i>';
                                    break;
                                case '1':
                                    published = '<i class="fa fa-circle-check text-success"></i>';
                                    break;
                                default:
                                    published = "N/A";
                            }
                            return published;
                        },
                        orderable: false,
                        width: 50
                    },
                    {
                        targets: [1],
                        width: "5%",
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        targets: [4],
                        render: function(data, type, row, meta) {
                            // Split the text based on the hyphen
                            var parts = data.split(' - ');

                            var platform;
                            switch (parts[0].toLowerCase()) {
                                case 'facebook':
                                    platform = '<i class="fab fa-facebook"></i> ' + data;
                                    break;
                                case 'youtube':
                                    platform = '<i class="fab fa-youtube"></i> ' + data;
                                    break;
                                case 'instagram':
                                    platform = '<i class="fab fa-instagram"></i> ' + data;
                                    break;
                                case 'tiktok':
                                    platform = '<i class="fab fa-tiktok"></i> ' + data;
                                    break;
                                default:
                                    platform = "N/A";
                            }
                            return platform;
                        }
                    },
                    {
                        targets: [11],
                        visible: false
                    },
                    {
                        targets: [12],
                        visible: false
                    }
                ],
                dom: "<'row'<'col-sm-12'<'text-center bg-body-light py-2 mb-2'B>>>" +
                    "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        title: 'ITN Digital Schedule',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12]
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        title: 'ITN Digital Schedule',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12]
                        },
                        filename: function() {
                            return getExportFileName()
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'ITN Digital Schedule',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12]
                        },
                        filename: function() {
                            return getExportFileName()
                        }
                    },
                    {
                        extend: 'csv',
                        text: 'CSV',
                        title: 'ITN Digital Schedule',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12]
                        },
                        filename: function() {
                            return getExportFileName()
                        }
                    }
                ],
            });
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('other-styles') ?>

<?= $this->endSection() ?>