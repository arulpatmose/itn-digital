<?php

/**
 * edit_schedule
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
        <div class="col-md-12 col-lg-12 col-xl-6">
            <form action="<?php echo site_url('schedules/update/' . $schedule['sched_id']); ?>" method="POST">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Schedule Information</h3>
                        <div class="block-options">
                            <button type="submit" class="btn btn-sm btn-primary">
                                Update
                            </button>
                            <button type="button" onclick="history.back()" class="btn btn-sm btn-danger">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row justify-content-center py-sm-3 py-md-5">
                            <div class="col-sm-10 col-md-8">
                                <div class="mb-4">
                                    <label class="form-label" for="commercial-id">Schedule ID</label>
                                    <input type="text" class="form-control" id="commercial-id" name="commercial-id" placeholder="Commercial ID" value="<?php echo $schedule['usched_id']; ?>" disabled readonly>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="schedule-commercial">Commercial</label>
                                    <select class="js-select2 form-control" id="schedule-commercial" name="schedule-commercial" style="width: 100%;" data-placeholder="Choose a Commercial">
                                        <option value="<?php echo $schedule['commercial']; ?>" selected="selected">
                                            <?php echo $commercial['name']; ?></option>
                                        <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="schedule-program">Program</label>
                                    <select class="js-select2 form-control" id="schedule-program" name="schedule-program" style="width: 100%;" data-placeholder="Choose a Program">
                                        <option value="<?php echo $schedule['program']; ?>" selected="selected">
                                            <?php echo $program['name']; ?></option>
                                        <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="schedule-platform">Platform</label>
                                    <select class="form-select" id="schedule-platform" name="schedule-platform" required>
                                        <option value="" selected disabled>Select a Platform</option>
                                        <?php foreach ($platforms as $platform) { ?>
                                            <option value="<?php echo $platform['id']; ?>" <?php echo $platform['id'] == $schedule['platform'] ? "selected" : ""; ?>>
                                                <?php echo $platform['name']; ?> - <?php echo $platform['channel']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="schedule-me">Marketing Executive</label>
                                    <input type="text" class="form-control" id="schedule-me" name="schedule-me" placeholder="Marketing Executive" value="<?php echo $schedule['marketing_ex']; ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="schedule-budget">Total Budget</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="number" class="form-control" id="schedule-budget" name="schedule-budget" placeholder="00" value="<?php echo $schedule['total_budget']; ?>" required>
                                        <span class="input-group-text">.00</span>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="schedule-remarks">Comments</label>
                                    <textarea class="form-control" id="schedule-remarks" name="schedule-remarks" rows="4" placeholder="Comments"><?php echo $schedule['remarks']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>