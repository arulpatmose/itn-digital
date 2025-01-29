<?php

/**
 * add_commercial
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
        <form action="<?php echo site_url('commercials/submit'); ?>" method="POST">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Commercial Information</h3>
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
                                <label class="form-label" for="commercial-name">Commercial Name</label>
                                <input type="text" class="form-control" id="commercial-name" name="commercial-name" placeholder="Commercial Name" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="commercial-duration">Duration</label>
                                <input type="number" class="form-control" id="commercial-duration" name="commercial-duration" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="commercial-format">Format</label>
                                <select class="form-select" id="commercial-format" name="commercial-format" required>
                                    <option value="" selected disabled>Select a Format</option>
                                    <?php foreach ($formats as $format) { ?>
                                        <option value="<?php echo $format['id']; ?>"><?php echo $format['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="commercial-category">Category</label>
                                <input type="text" class="form-control" id="commercial-category" name="commercial-category" placeholder="Category" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="commercial-sub-category">Sub Category</label>
                                <input type="text" class="form-control" id="commercial-sub-category" name="commercial-sub-category" placeholder="Sub Category" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="commercial-client">Client</label>
                                <select class="js-select2 form-select" id="commercial-client" name="commercial-client" style="width: 100%;" data-placeholder="Select a Client" required>
                                    <option value="" selected disabled>Select a Client</option>
                                    <?php foreach ($clients as $client) { ?>
                                        <option value="<?php echo $client['id']; ?>"><?php echo $client['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="commercial-remarks">Comments</label>
                                <textarea class="form-control" id="commercial-remarks" name="commercial-remarks" rows="4" placeholder="Comments"></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="commercial-link">Download Link</label>
                                <input type="url" class="form-control" id="commercial-link" name="commercial-link" placeholder="Download Link">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>