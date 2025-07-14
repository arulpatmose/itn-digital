<?php

/**
 * add_schedule
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
            <form action="<?php echo site_url('schedule/submit/') . $schedule['sched_id']; ?>" method="POST">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Schedule Information for <span
                                class="mx-1 text-success"><?php echo $schedule['usched_id']; ?></span></h3>
                        <div class="block-options">
                            <button type="submit" class="btn btn-sm btn-primary">
                                Save
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
                                    <label class="form-label" for="schedule-spot">Spot</label>
                                    <select class="js-select2 form-select" id="schedule-spot" name="schedule-spot"
                                        style="width: 100%;" data-placeholder="Select a Spot" required>
                                        <option></option>
                                        <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="schedule-dates">Date(s)</label>
                                    <input type="text" class="js-flatpickr form-control" name="schedule-dates"
                                        id="schedule-item-dates" placeholder="Select dates" autocomplete="off" required
                                        data-disabled="<?php echo $disabledDates; ?>">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="schedule-remarks">Comments</label>
                                    <textarea class="form-control" id="schedule-remarks" name="schedule-remarks" rows="4"
                                        placeholder="Comments"></textarea>
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