<?php

/**
 * edit_program
 *
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
            <form action="<?php echo site_url('programs/update/' . $program['prog_id']); ?>" method="POST" enctype="multipart/form-data">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Program Information</h3>
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
                                    <label class="form-label" for="program-name">Program Name</label>
                                    <input type="text" class="form-control" id="program-name" name="program-name" placeholder="Program Name" value="<?php echo $program['name']; ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Type</label>
                                    <div class="space-y-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="program-type-1" name="program-type" value="0" <?php echo $program['type'] == 0 ? 'checked' : ""; ?> required>
                                            <label class="form-check-label" for="program-type-1">Teledrama</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="program-type-2" name="program-type" value="1" <?php echo $program['type'] == 1 ? 'checked' : ""; ?> required>
                                            <label class="form-check-label" for="program-type-2">TV Show</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="program-type-3" name="program-type" value="2" <?php echo $program['type'] == 2 ? 'checked' : ""; ?> required>
                                            <label class="form-check-label" for="program-type-3">Other</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="program-thumbnail">Thumbnail</label>
                                    <input class="form-control" type="file" id="program-thumbnail" name="program-thumbnail">
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-4">
                                <img class="w-100 rounded animated bounceIn" alt="Program Thumbnail" src="<?php echo base_url('uploads/thumbnails/' . $thumbImage); ?>">
                                <?php if (isset($program['thumbnail'])) { ?>
                                    <div class="mt-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="remove-thumbnail" name="remove-thumbnail">
                                            <label class="form-check-label" for="remove-thumbnail">Remove Thumbnail
                                                Image?</label>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>