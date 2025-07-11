<?php

/**
 * view_accounts
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
                        List of Budget Allocation for Commercials
                    </h3>
                    <div class="block-options">

                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <label class="form-label" for="schedule-date-range">Date Range<button type="reset"
                                    class="btn bg-transparent btn-sm"
                                    onclick="clearInput('schedule-date-range')">Reset</button></label>
                            <div class="mb-4 input-group">
                                <input type="search" class="js-flatpickr form-control" name="schedule-date-range"
                                    id="schedule-date-range" placeholder="Choose Dates">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="schedule-filters-client">Client<button type="reset"
                                        class="btn bg-transparent btn-sm"
                                        onclick="clearSelection('schedule-filters-client')">Reset</button></label>
                                <select class="js-select2 form-control" id="schedule-filters-client"
                                    name="schedule-filters-client" style="width: 100%;" data-placeholder="Choose a Client">
                                    <option></option>
                                    <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="schedule-filters-commercial">Commercial<button type="reset"
                                        class="btn bg-transparent btn-sm"
                                        onclick="clearSelection('schedule-filters-commercial')">Reset</button></label>
                                <select class="js-select2 form-control" id="schedule-filters-commercial"
                                    name="schedule-filters-commercial" style="width: 100%;"
                                    data-placeholder="Choose a Commercial">
                                    <option></option>
                                    <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4 input-group">
                                <label class="form-label" for="schedule-filters-program">Program<button type="reset"
                                        class="btn bg-transparent btn-sm"
                                        onclick="clearSelection('schedule-filters-program')">Reset</button></label>
                                <select class="js-select2 form-control" id="schedule-filters-program"
                                    name="schedule-filters-program" style="width: 100%;"
                                    data-placeholder="Choose a Program">
                                    <option></option>
                                    <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="schedule-filters-platform">Platform<button type="reset"
                                        class="btn bg-transparent btn-sm"
                                        onclick="clearSelection('schedule-filters-platform')">Reset</button></label>
                                <select class="js-select2 form-select" id="schedule-filters-platform"
                                    name="schedule-filters-platform" data-placeholder="Choose a Platform">
                                    <option></option>
                                    <?php foreach ($platforms as $platform) { ?>
                                        <option value="<?php echo $platform['id']; ?>"><?php echo $platform['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="schedule-filters-format">Format<button type="reset"
                                        class="btn bg-transparent btn-sm"
                                        onclick="clearSelection('schedule-filters-format')">Reset</button></label>
                                <select class="js-select2 form-select" id="schedule-filters-format"
                                    name="schedule-filters-format" data-placeholder="Choose a Format">
                                    <option></option>
                                    <?php foreach ($formats as $format) { ?>
                                        <option value="<?php echo $format['id']; ?>"><?php echo $format['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-vcenter" id="table-schedule-budget" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Schedule ID</th>
                                <th>Commercial</th>
                                <th>Client</th>
                                <th>Program</th>
                                <th>Format</th>
                                <th>Platform</th>
                                <th>Merketing Executive</th>
                                <th>Number of Ads</th>
                                <th>Daily Budget</th>
                                <th>Total Budget</th>
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
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script src="<?= base_url('assets/js/plugins/datatables-buttons-jszip/jszip.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins/datatables-buttons/buttons.print.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/plugins/datatables-buttons/buttons.html5.min.js'); ?>"></script>

<script>
    // Datatable for Schedule Accounts Data
    jQuery(document).ready(function() {
        if (jQuery('#table-schedule-budget').length) {
            var tableScheduleBudget = jQuery('#table-schedule-budget').DataTable({
                ajax: {
                    url: '/api/get-schedules-budget',
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
                columns: [{
                        data: 'sched_id'
                    },
                    {
                        data: 'usched_id'
                    },
                    {
                        data: 'commercial'
                    },
                    {
                        data: 'client_name'
                    },
                    {
                        data: 'program'
                    },
                    {
                        data: 'format'
                    },
                    {
                        data: 'platform'
                    },
                    {
                        data: 'marketing_ex'
                    },
                    {
                        data: 'num_schedules'
                    },
                    {
                        data: 'daily_budget'
                    },
                    {
                        data: 'total_budget'
                    }
                ],
                columnDefs: [{
                        targets: [0],
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        targets: [6],
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
                        render: function(data, type, row, meta) {
                            if (data !== 0.00 || data !== null) {
                                return data.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            } else {
                                return data;
                            }
                        },
                        className: 'dt-body-right'
                    },
                    {
                        targets: [10],
                        render: function(data, type, row, meta) {
                            if (data !== 0.00 || data !== null) {
                                return data.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            } else {
                                return data;
                            }
                        },
                        className: 'dt-body-right'
                    }
                ],
                dom: "<'row'<'col-sm-12'<'text-center bg-body-light py-2 mb-2'B>>>" +
                    "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    searchPlaceholder: "Enter Schedule ID"
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('other-styles') ?>

<?= $this->endSection() ?>