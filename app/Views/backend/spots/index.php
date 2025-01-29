<?php

/**
 * view_spots
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
                    List of Spots
                </h3>
                <div class="block-options">
                    <a href="<?php echo base_url('spots/add'); ?>" role="button" class="btn btn-sm btn-primary">Add
                        New</a>
                </div>
            </div>
            <div class="block-content block-content-full">
                <table class="table table-striped table-vcenter" id="table-spots">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Name</th>
                            <th>Priority</th>
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