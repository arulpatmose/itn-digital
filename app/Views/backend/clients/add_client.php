<?php

/**
 * add_client
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
            <form action="<?php echo site_url('clients/submit'); ?>" method="POST">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Client Information</h3>
                        <div class="block-options">
                            <a role="button" href="<?php echo base_url('clients'); ?>" class="btn btn-sm btn-danger">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary">
                                Save
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row justify-content-center py-sm-3 py-md-5">
                            <div class="col-sm-10 col-md-8">
                                <div class="mb-4">
                                    <label class="form-label" for="client-name">Client Name</label>
                                    <input type="text" class="form-control" id="client-name" name="client-name" placeholder="Client Name" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="client-address">Address</label>
                                    <input type="text" class="form-control" id="client-address" name="client-address" placeholder="Address" required>
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