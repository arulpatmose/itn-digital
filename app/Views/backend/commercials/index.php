<?php

/**
 * view_commercials
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
                    List of Commercials
                </h3>
                <div class="block-options">
                    <a href="<?php echo base_url('commercials/add'); ?>" role="button" class="btn btn-sm btn-primary">Add
                        New</a>
                </div>
            </div>
            <div class="block-content block-content-full">
                <table class="table table-striped table-vcenter" id="table-commercials">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Commercial ID</th>
                            <th>Name</th>
                            <th>Duration</th>
                            <th>Format</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Client</th>
                            <th>Added by</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>