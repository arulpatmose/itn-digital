<?php

/**
 * view_schedules
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
                        List of Scheduled Commercials
                    </h3>
                    <div class="block-options">
                        <a href="<?php echo base_url('schedules/add'); ?>" role="button" class="btn btn-sm btn-primary">Add
                            New</a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <label class="form-label d-flex justify-content-between align-items-center" for="schedule-date-range">Date Range<button type="reset" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearInput('schedule-date-range')">Reset</button></label>
                            <div class="mb-4 input-group">
                                <input type="search" class="js-flatpickr form-control" name="schedule-date-range" id="schedule-date-range" placeholder="Choose Dates">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="schedule-filters-client">Client<button type="reset" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearSelection('schedule-filters-client')">Reset</button></label>
                                <select class="js-select2 form-control" id="schedule-filters-client" name="schedule-filters-client" style="width: 100%;" data-placeholder="Choose a Client">
                                    <option></option>
                                    <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="schedule-filters-commercial">Commercial<button type="reset" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearSelection('schedule-filters-commercial')">Reset</button></label>
                                <select class="js-select2 form-control" id="schedule-filters-commercial" name="schedule-filters-commercial" style="width: 100%;" data-placeholder="Choose a Commercial">
                                    <option></option>
                                    <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="schedule-filters-program">Program<button type="reset" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearSelection('schedule-filters-program')">Reset</button></label>
                                <select class="js-select2 form-control" id="schedule-filters-program" name="schedule-filters-program" style="width: 100%;" data-placeholder="Choose a Program">
                                    <option></option>
                                    <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="schedule-filters-platform">Platform<button type="reset" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearSelection('schedule-filters-platform')">Reset</button></label>
                                <select class="js-select2 form-select" id="schedule-filters-platform" name="schedule-filters-platform" data-placeholder="Choose a Platform">
                                    <option></option>
                                    <?php foreach ($platforms as $platform) { ?>
                                        <option value="<?php echo $platform['id']; ?>">
                                            <?php echo $platform['name'] . ' - ' . $platform['channel']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="schedule-filters-format">Format<button type="reset" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearSelection('schedule-filters-format')">Reset</button></label>
                                <select class="js-select2 form-select" id="schedule-filters-format" name="schedule-filters-format" data-placeholder="Choose a Format">
                                    <option></option>
                                    <?php foreach ($formats as $format) { ?>
                                        <option value="<?php echo $format['id']; ?>"><?php echo $format['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-schedules">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-center"></th>
                                    <th class="text-center">#</th>
                                    <th>Schedule ID</th>
                                    <th>Commercial</th>
                                    <th>Format</th>
                                    <th>Client</th>
                                    <th>Program</th>
                                    <th>Platform</th>
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
<script src="<?= base_url('assets/js/plugins/datatables-buttons-jszip/jszip.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins/datatables-buttons/buttons.print.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins/datatables-buttons/buttons.html5.min.js'); ?>"></script>

<script>
    // Datatable for Schedules
    jQuery(document).ready(function() {
        if (jQuery('#table-schedules').length) {
            var tableSchedules = jQuery('#table-schedules').DataTable({
                ajax: {
                    url: '/api/get-all-schedules',
                    data: function(data) {
                        // Custom filter values
                        var dateRange = $('#schedule-date-range').val();
                        var program = $('#schedule-filters-program').val();
                        var commercial = $('#schedule-filters-commercial').val();
                        var platform = $('#schedule-filters-platform').val();
                        var client = $('#schedule-filters-client').val();
                        var format = $('#schedule-filters-format').val();
                        var schedule = $('#schedule-filters-schedule').val();

                        // Append values to Data
                        data.dateRange = dateRange;
                        data.program = program;
                        data.commercial = commercial
                        data.platform = platform;
                        data.client = client;
                        data.format = format;
                        data.schedule = schedule;

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
                autoWidth: true,
                scrollX: true,
                scrollResize: true,
                scrollCollapse: true,
                // responsive: true,
                stateSave: true,
                searching: true,
                order: [
                    [3, 'desc']
                ],
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'published'
                    },
                    {
                        data: 'sched_id'
                    },
                    {
                        data: 'usched_id'
                    },
                    {
                        data: 'commercial'
                    },
                    {
                        data: 'format'
                    },
                    {
                        data: 'client_name'
                    },
                    {
                        data: 'program'
                    },
                    {
                        data: 'platform'
                    },
                    {
                        data: 'sched_id'
                    }

                ],
                columnDefs: [{
                        targets: [1],
                        render: function(data, type, row, meta) {
                            var published;
                            switch (row.published) {
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
                        targets: [2],
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        width: 50
                    },
                    {
                        targets: [3],
                        render: function(data, type, row, meta) {
                            var uri = URI().origin();

                            return '<a class="link-fx text-success" href="' + uri + '/schedule/' + row.sched_id + '">' + data + '</a>';
                        }
                    },
                    {
                        targets: [4],
                        width: 120
                    },
                    {
                        targets: [8],
                        render: function(data, type, row, meta) {
                            var platform;
                            switch (row.platform.toLowerCase()) {
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
                            return platform + ' (' + row.channel + ')';
                        }
                    },
                    {
                        targets: [9],
                        orderable: false,
                        render: function(data, type, row, meta) {
                            var uri = URI().origin();

                            return '<div class="btn-group">' +
                                '<a role="button" class="btn btn-sm btn-success" data-id="' + data + '" href="' + uri + '/schedules/edit/' + data + '" data-bs-toggle="tooltip" aria-label="Edit Schedule" data-bs-title="Edit Schedule" data-bs-placement="left">' +
                                '<i class="fa fa-fw fa-pencil-alt"></i>' + '</a>' +
                                '<a role="button" class="btn btn-sm btn-danger" id="delete-schedule-button" data-schedule="' + row.usched_id + '"data-id="' + data + '" href="#" data-url="' + _AppUri + '/schedules/delete' + '" data-bs-toggle="tooltip" aria-label="Remove Schedule" data-bs-title="Remove Schedule" data-bs-placement="top">' +
                                '<i class="fa fa-fw fa-times"></i>' + '</a>' +
                                '<a role="button" class="btn btn-sm btn-primary" data-id="' + data + '" href="' + uri + '/schedule/' + data + '" data-bs-toggle="tooltip" aria-label="View Items" data-bs-title="View Items" data-bs-placement="right">' +
                                '<i class="fa fa-fw fa-eye"></i>' + '</a>' + ' </div>';
                        }
                    }
                ],
                language: {
                    searchPlaceholder: "Enter Schedule ID"
                }
            });

            // Add event listener for opening and closing details
            tableSchedules.on('click', 'td.dt-control', function(e) {
                let tr = e.target.closest('tr');
                let row = tableSchedules.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(formatScheduleExtra(row.data())).show();
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    // Delete Schedules Function
    jQuery(document).on('click', '#delete-schedule-button', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var url = $(this).data('url');
        var schedule = $(this).data('schedule');
        toast.fire({
            title: "Are you sure?",
            html: "<span class=\"my-3 d-block\">You're about to delete <mark>" + schedule + "</mark></span><small>Deleting a single schedule item will result in the removal of all associated scheduled items for this commercial. This action is irreversible.</small>",
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonText: "Yes, Delete All",
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
                            jQuery('#table-schedules').DataTable().ajax.reload(null, false);
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