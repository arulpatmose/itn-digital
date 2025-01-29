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
                <table class="table table-striped table-vcenter" id="table-schedule">
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
<?= $this->endSection() ?>