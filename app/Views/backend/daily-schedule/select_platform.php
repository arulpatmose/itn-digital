<?php

/**
 * view_select_platform
 * Author: Arul Patmose
 *
 * Load Default Global Template and Extend
 *
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>

<!-- Platform Selection Block Modal -->
<div class="modal fade" id="select-platform-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-transparent mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Choose Your Platform</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <form id="schedule-update-form" action="">
                        <div class="mb-4">
                            <label class="form-label" for="schedule-link">Reference Link</label>
                            <input type="url" class="form-control" id="schedule-link" name="schedule-link" placeholder="Reference Link">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="schedule-remarks">Comments</label>
                            <textarea class="form-control" id="schedule-remarks" name="schedule-remarks" rows="4" placeholder="Comments"></textarea>
                        </div>
                    </form>
                </div>
                <div class="block-content block-content-full text-end bg-body">
                    <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary" id="schedule-update-form-button">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Platform Selection Block Modal -->

<?= $this->endSection() ?>