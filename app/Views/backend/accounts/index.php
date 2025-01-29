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
<?= $this->endSection() ?>