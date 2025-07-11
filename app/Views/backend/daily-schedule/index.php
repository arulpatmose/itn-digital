<?php

/**
 * view_daily_schedule
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
            <div class="block block-rounded" data-controller="<?php echo $controller; ?>" id="daily-schedule-items-wrapper">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        List of Scheduled Commercials - <?php echo "(" . $schedule_date . ")"; ?>
                    </h3>
                    <div class="block-options">
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <label class="form-label" for="daily-schedule-date">Date</label>
                            <div class="mb-4 input-group">
                                <input type="text" class="js-flatpickr form-control" name="daily-schedule-date" id="daily-schedule-date" placeholder="Select a date" data-schedule-date="<?php echo "(" . $date . ")"; ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="schedule-filters-platform">Platform<button type="reset" class="btn bg-transparent border-0 btn-alt-secondary btn-sm py-0" onclick="clearSelection('schedule-filters-platform')">Reset</button></label>
                                <select class="js-select2 form-select" id="schedule-filters-platform" name="schedule-filters-platform" data-placeholder="Choose a Platform">
                                    <option></option>
                                    <?php foreach ($platforms as $platform) { ?>
                                        <option value="<?php echo $platform['id']; ?>" <?php echo ($selectedPlatform === $platform['id']) ? 'selected' : ''; ?>>
                                            <?php echo $platform['name'] . ' - ' . $platform['channel']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($schedules) && !is_null($schedules) && !empty($schedules)) {
                        foreach ($schedules as $index => $schedule) {
                    ?>
                            <div class="block block-fx-shadow">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title"><?php echo $schedule['program']['program_name']; ?></h3>
                                    <div class="block-options">
                                        <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="block-option" data-action="fullscreen_toggle"><i class="si si-size-fullscreen"></i></button>
                                        <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="block-option" data-action="content_toggle"><i class="si si-arrow-up"></i></button>
                                        <a href="#" role="button" id="bulk-update-button" class="btn btn-sm btn-primary bulk-update-button" data-table-id="<?php echo $index + 1; ?>">Update</a>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-3 col-sm-12">
                                            <?php if (isset($schedule['program']['thumbnail']) && !is_null(isset($schedule['program']['thumbnail']))) { ?>
                                                <img class="w-100 rounded animated bounceIn" alt="Program Thumbnail" src="<?php echo base_url('uploads/thumbnails/' . $schedule['program']['thumbnail']); ?>">
                                            <?php } else { ?> <img class="w-100 rounded animated bounceIn" alt="Program Thumbnail" src="<?php echo base_url('uploads/thumbnails/No-Image-Placeholder.svg') ?>">
                                            <?php  } ?>
                                        </div>
                                        <div class="col-md-9 col-lg-9 col-sm-12" data-table-id="<?php echo $index + 1; ?>">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover w-100 daily-schedule-items-table" id="<?php echo 'data-table-' . $index + 1; ?>">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center"></th>
                                                            <th class="text-center"><input type="checkbox" class="form-check-input select-all" data-bs-toggle="tooltip" aria-label="Select All" data-bs-title="Select All" data-bs-placement="top"></th>
                                                            <th class="text-center">#</th>
                                                            <th>Commercial</th>
                                                            <th>Format</th>
                                                            <th>Platform</th>
                                                            <th>Spot</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php for ($i = 0; $i < count($schedule['schedule']); $i++) { ?>
                                                            <tr>
                                                                <td class="text-center">
                                                                    <?php
                                                                    $published = "";
                                                                    switch ($schedule['schedule'][$i]['published']) {
                                                                        case '0':
                                                                            $published = '<i class="fa fa-times-circle text-danger"></i>';
                                                                            break;
                                                                        case '1':
                                                                            $published = '<i class="fa fa-circle-check text-success"></i>';
                                                                            break;
                                                                        default:
                                                                            $published = '';
                                                                    }
                                                                    echo $published;
                                                                    ?>
                                                                </td>
                                                                <td class="text-center"><input type="checkbox" class="select-row form-check-input" data-id="<?php echo $schedule['schedule'][$i]['scd_id']; ?>"></td>
                                                                <td class="text-center"><?php echo $i + 1; ?></td>
                                                                <td>
                                                                    <a class="link-fx text-success" data-bs-toggle="tooltip" aria-label="View Schedule" data-bs-title="View Schedule" data-bs-placement="top" href="<?php echo base_url('schedule/') . $schedule['schedule'][$i]['sched_id']; ?>"><?php echo $schedule['schedule'][$i]['commercial']; ?></a>
                                                                    <span class="mx-1 text-gray-dark">(<?php echo $schedule['schedule'][$i]['duration']; ?>s)</span>
                                                                    <?php if (isset($schedule['schedule'][$i]['category'])) { ?>
                                                                        <span class="badge bg-info"><?php echo $schedule['schedule'][$i]['category']; ?></span>
                                                                    <?php } ?>
                                                                    <?php if (isset($schedule['schedule'][$i]['sub_category'])) { ?>
                                                                        <span class="badge bg-success"><?php echo $schedule['schedule'][$i]['sub_category']; ?></span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $schedule['schedule'][$i]['format']; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $platform = "";
                                                                    switch (strtolower($schedule['schedule'][$i]['platform'])) {
                                                                        case 'facebook':
                                                                            $platform = '<i class="fab fa-facebook"></i> ';
                                                                            break;
                                                                        case 'youtube':
                                                                            $platform = '<i class="fab fa-youtube"></i> ';
                                                                            break;
                                                                        case 'instagram':
                                                                            $platform = '<i class="fab fa-instagram"></i> ';
                                                                            break;
                                                                        case 'tiktok':
                                                                            $platform = '<i class="fab fa-tiktok"></i> ';
                                                                            break;
                                                                        default:
                                                                            $platform = '<i class="far fa-circle-question"></i>';
                                                                    }
                                                                    echo $platform;
                                                                    ?>
                                                                    <?php echo $schedule['schedule'][$i]['platform'] . ' (' . $schedule['schedule'][$i]['channel'] . ')'; ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $schedule['schedule'][$i]['spot']; ?>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <a role="button" class="btn btn-sm btn-success" id="schedule-update-button" data-id="<?php echo $schedule['schedule'][$i]['scd_id']; ?>" data-url="<?php echo base_url('/daily-schedule/update/' . $schedule['schedule'][$i]['scd_id']); ?>" href="#" data-bs-toggle="tooltip" aria-label="Update Status" data-bs-title="Update Status" data-bs-placement="left">
                                                                            <i class="fa fa-fw fa-pencil-alt"></i>
                                                                        </a>
                                                                        <a role="button" class="btn btn-sm btn-primary" id="reference-link-button" data-id="<?php echo $schedule['schedule'][$i]['scd_id']; ?>" href="#" data-url="<?php echo $schedule['schedule'][$i]['link']; ?>" data-bs-toggle="tooltip" aria-label="View Program" data-bs-title="View Program" data-bs-placement="right">
                                                                            <i class="fa fa-fw fa-link"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="row">
                            <div class="col">
                                <p class="text-center">"No schedules are available!"</p>
                                <p class="text-center"><a class="link-fx text-info" href="<?php echo base_url('schedules/add'); ?>">Create a Schedule</a></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Update Form Block Modal -->
<div class="modal fade" id="schedule-update-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-transparent mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Update Schedule</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <form id="schedule-update-form" action="" autocomplete="off">
                        <div class="mb-4">
                            <label class="form-label" for="schedule-link">Reference Link</label>
                            <input type="url" class="form-control" id="schedule-link" name="schedule-link" placeholder="Reference Link">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="schedule-remarks">Comments</label>
                            <textarea class="form-control" id="schedule-remarks" name="schedule-remarks" rows="4" placeholder="Comments"></textarea>
                        </div>
                    </form>
                </div>
                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="schedule-update-form-button">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Schedule Update Form Block Modal -->
<?= $this->endSection() ?>