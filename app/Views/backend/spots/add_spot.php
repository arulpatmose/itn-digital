<?php

/**
 * add_spot
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
    <div class="col-md-12 col-lg-12 col-xl-6">
        <form action="<?php echo site_url('spots/submit'); ?>" method="POST">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Spot Information</h3>
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
                                <label class="form-label" for="spot-name">Spot Name</label>
                                <input type="text" class="form-control" id="spot-name" name="spot-name" placeholder="Spot Name" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="spot-priority">Priority</label>
                                <input type="number" class="form-control" id="spot-priority" name="spot-priority" placeholder="Priority" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>