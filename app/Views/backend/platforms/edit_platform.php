<?php

/**
 * edit_platform
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
        <form action="<?php echo site_url('platforms/update/' . $platform['pfm_id']); ?>" method="POST">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Platform Information</h3>
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
                                <label class="form-label" for="platform-name">Platform Name</label>
                                <input type="text" class="form-control" id="platform-name" name="platform-name"
                                    placeholder="Platform Name" value="<?php echo $platform['name']; ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="platform-channel">Channel</label>
                                <input type="text" class="form-control" id="platform-channel" name="platform-channel"
                                    placeholder="Channel" value="<?php echo $platform['channel']; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>