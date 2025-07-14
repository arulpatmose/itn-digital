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
                                        <div class="col-md-3 col-lg-3 col-sm-12 position-relative">
                                            <div class="position-sticky pb-3" style="top: 5rem;">
                                                <img
                                                    class="w-100 rounded animated bounceIn"
                                                    alt="Program Thumbnail"
                                                    src="<?= isset($schedule['program']['thumbnail']) && !empty($schedule['program']['thumbnail'])
                                                                ? base_url('uploads/thumbnails/' . $schedule['program']['thumbnail'])
                                                                : base_url('uploads/thumbnails/No-Image-Placeholder.svg') ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-9 col-lg-9 col-sm-12" data-table-id="<?php echo $index + 1; ?>">
                                            <?php foreach ($schedule['schedule'] as $platform): ?>
                                                <?php
                                                $platformNameLower = strtolower($platform['platform_name']);
                                                $platformIcon = match ($platformNameLower) {
                                                    'facebook' => '<i class="fab fa-facebook"></i>',
                                                    'youtube'  => '<i class="fab fa-youtube"></i>',
                                                    'instagram' => '<i class="fab fa-instagram"></i>',
                                                    'tiktok'   => '<i class="fab fa-tiktok"></i>',
                                                    default    => '<i class="far fa-circle-question"></i>',
                                                };
                                                ?>
                                                <h5 class="mt-4">
                                                    <?= $platformIcon ?>
                                                    <?= esc($platform['platform_name']) ?> (<?= esc($platform['channel']) ?>)
                                                </h5>

                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover w-100 daily-schedule-items-table" id="data-table-<?= $loopIndex = isset($loopIndex) ? $loopIndex + 1 : 1; ?>">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center"></th>
                                                                <th class="text-center">
                                                                    <input type="checkbox" class="form-check-input select-all" data-bs-toggle="tooltip" aria-label="Select All" data-bs-title="Select All" data-bs-placement="top">
                                                                </th>
                                                                <th class="text-center">#</th>
                                                                <th>Commercial</th>
                                                                <th>Format</th>
                                                                <th>Spot</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($platform['items'] as $i => $item): ?>
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <?php
                                                                        echo match ($item['published']) {
                                                                            '0' => '<i class="fa fa-times-circle text-danger"></i>',
                                                                            '1' => '<i class="fa fa-circle-check text-success"></i>',
                                                                            default => ''
                                                                        };
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <input type="checkbox" class="select-row form-check-input" data-id="<?= esc($item['scd_id']) ?>">
                                                                    </td>
                                                                    <td class="text-center"><?= $i + 1 ?></td>
                                                                    <td>
                                                                        <a class="link-fx text-success" href="<?= base_url('schedule/' . $item['sched_id']) ?>" data-bs-toggle="tooltip" aria-label="View Schedule" data-bs-title="View Schedule" data-bs-placement="top">
                                                                            <?= esc($item['commercial']) ?>
                                                                        </a>
                                                                        <span class="mx-1 text-gray-dark">(<?= esc($item['duration']) ?>s)</span>
                                                                        <?php if (!empty($item['category'])): ?>
                                                                            <span class="badge bg-info"><?= esc($item['category']) ?></span>
                                                                        <?php endif ?>
                                                                        <?php if (!empty($item['sub_category'])): ?>
                                                                            <span class="badge bg-success"><?= esc($item['sub_category']) ?></span>
                                                                        <?php endif ?>
                                                                    </td>
                                                                    <td><?= esc($item['format']) ?></td>
                                                                    <td><?= esc($item['spot']) ?></td>
                                                                    <td>
                                                                        <div class="btn-group">
                                                                            <a role="button"
                                                                                class="btn btn-sm btn-success"
                                                                                id="schedule-update-button"
                                                                                data-id="<?= esc($item['scd_id']) ?>"
                                                                                data-url="<?= base_url('/daily-schedule/update/' . $item['scd_id']) ?>"
                                                                                href="#"
                                                                                data-bs-toggle="tooltip"
                                                                                aria-label="Update Status"
                                                                                data-bs-title="Update Status"
                                                                                data-bs-placement="left">
                                                                                <i class="fa fa-fw fa-pencil-alt"></i>
                                                                            </a>

                                                                            <a role="button"
                                                                                class="btn btn-sm btn-warning"
                                                                                id="view-comments-button"
                                                                                data-schedule-id="<?= esc($item['sched_id']) ?>"
                                                                                data-schedule-item-id="<?= esc($item['scd_id']) ?>"
                                                                                href="#"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#viewCommentsModal">
                                                                                <i class="fa fa-fw fa-comments"></i>
                                                                            </a>

                                                                            <a role="button"
                                                                                class="btn btn-sm btn-primary"
                                                                                id="reference-link-button"
                                                                                data-id="<?= esc($item['scd_id']) ?>"
                                                                                href="#"
                                                                                data-url="<?= esc($item['link']) ?>"
                                                                                data-bs-toggle="tooltip"
                                                                                aria-label="View Program"
                                                                                data-bs-title="View Program"
                                                                                data-bs-placement="right">
                                                                                <i class="fa fa-fw fa-link"></i>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endforeach; ?>
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
    <div class="modal-dialog modal-dialog-centered modal-dialog-popin" role="document">
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

<!-- View Comments Modal -->
<div class="modal fade" id="viewCommentsModal" tabindex="-1" aria-labelledby="viewCommentsModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCommentsModalLabel">Remarks & Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Schedule Remarks -->
                <h6 class="fw-bold mb-2">📝 Schedule Remarks</h6>
                <ul class="list-group text-start" id="schedule-remarks-list"></ul>
                <div class="text-muted text-center py-2 d-none" id="no-schedule-remarks-msg">
                    No remarks added for this schedule.
                </div>
                <hr class="my-2">
                <!-- Schedule Item Comments -->
                <h6 class="fw-bold mb-2">💬 Item-Specific Comments</h6>
                <ul class="list-group text-start" id="schedule-item-comments-list"></ul>
                <div class="text-muted text-center py-2 d-none" id="no-item-comments-msg">
                    No comments for this schedule item.
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END View Comments Modal -->
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    $(document).on('click', '#view-comments-button', function(e) {
        e.preventDefault();

        const button = $(this);
        const scheduleId = button.data('schedule-id');
        const scheduleItemId = button.data('schedule-item-id');

        // Elements
        const remarksList = $('#schedule-remarks-list').empty();
        const itemCommentsList = $('#schedule-item-comments-list').empty();

        $('#no-schedule-remarks-msg, #no-item-comments-msg').addClass('d-none');

        $.ajax({
            url: '<?= base_url('daily-schedule/fetch-comments') ?>',
            method: 'POST',
            data: {
                schedule_id: scheduleId,
                item_id: scheduleItemId
            },
            dataType: 'json',
            success: function(res) {
                // Render schedule remarks
                if (res.schedule_remarks) {
                    remarksList.append(
                        `<div class="text-muted text-center py-2">${res.schedule_remarks}</div>`
                    );
                } else {
                    $('#no-schedule-remarks-msg').removeClass('d-none');
                }

                // Render item comments
                if (res.item_comments) {
                    itemCommentsList.append(
                        `<div class="text-muted text-center py-2">${res.item_comments}</div>`
                    );
                } else {
                    $('#no-item-comments-msg').removeClass('d-none');
                }

                $('#viewCommentsModal').modal('show');
            },
            error: function(xhr) {
                toast.fire({
                    title: 'Failed',
                    icon: 'error',
                    html: xhr.responseJSON?.message || 'Something went wrong.',
                    showCancelButton: false,
                    showConfirmButton: true
                }).then(() => window.location.reload());
            }
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('other-styles') ?>
<style>
    .daily-schedule-items-table th:nth-child(1),
    /* status icon */
    .daily-schedule-items-table td:nth-child(1) {
        width: 40px;
        /* fixed small width */
    }

    .daily-schedule-items-table th:nth-child(2),
    .daily-schedule-items-table td:nth-child(2) {
        width: 40px;
        /* checkbox column */
    }

    .daily-schedule-items-table th:nth-child(3),
    .daily-schedule-items-table td:nth-child(3) {
        width: 40px;
        /* serial number */
    }

    .daily-schedule-items-table th:nth-child(4),
    .daily-schedule-items-table td:nth-child(4) {
        min-width: 220px;
        /* commercial */
    }

    .daily-schedule-items-table th:nth-child(5),
    .daily-schedule-items-table td:nth-child(5) {
        width: 120px;
        /* format */
    }

    .daily-schedule-items-table th:nth-child(6),
    .daily-schedule-items-table td:nth-child(6) {
        width: 250px;
        /* spot */
    }

    .daily-schedule-items-table th:nth-child(7),
    .daily-schedule-items-table td:nth-child(7) {
        width: 120px;
        /* actions */
    }

    @media (max-width: 767.98px) {

        /* Bootstrap’s sm breakpoint */
        .daily-schedule-items-table td,
        .daily-schedule-items-table th {
            white-space: nowrap !important;
            /* Prevent wrapping */
            overflow: hidden !important;
            /* Hide overflow */
            text-overflow: ellipsis !important;
            /* Show ... for overflow */
        }
    }
</style>
<?= $this->endSection() ?>