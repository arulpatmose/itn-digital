<?php

/**
 * general
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
        <div class="col-md-12">
            <form class="form-horizontal form-submit-event" action="<?= base_url('settings/update') ?>" method="POST" id="settings-form">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Authentication Settings</h3>
                        <div class="block-options">
                            <button type="submit" class="btn btn-sm btn-primary">
                                Save
                            </button>
                            <a type="button" class="btn btn-sm btn-danger" href="<?= base_url('settings') ?>">Cancel</a>
                        </div>
                    </div>

                    <div class="block-content">
                        <div class="row push g-4">
                            <?php foreach ($fields as $key => $field): ?>
                                <div class="col-md-12">
                                    <label for="<?= $key ?>" class="col-form-label"><?= $field['label'] ?></label>
                                    <?php if ($field['type'] === 'select'): ?>
                                        <select name="settings[<?= $key ?>]" id="<?= $key ?>" class="form-control">
                                            <?php foreach ($field['options'] as $val => $label): ?>
                                                <option value="<?= $val ?>"
                                                    <?= isset($settings[$key]) && $settings[$key] == $val ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($field['type'] === 'textarea'): ?>
                                        <textarea name="settings[<?= $key ?>]" id="<?= $key ?>"
                                            class="form-control"
                                            rows="3"><?= isset($settings[$key]) ? esc($settings[$key]) : '' ?></textarea>
                                    <?php else: ?>
                                        <input type="<?= $field['type'] ?>"
                                            name="settings[<?= $key ?>]"
                                            id="<?= $key ?>"
                                            class="form-control"
                                            value="<?= isset($settings[$key]) ? esc($settings[$key]) : '' ?>"
                                            placeholder="<?= $field['label'] ?>" />
                                    <?php endif; ?>
                                    <a href="<?= base_url('settings/forget/' . $settingGroup . '/' . $key) ?>"
                                        class="text-danger mt-2 d-block reset-setting"
                                        data-url="<?= base_url('settings/forget/' . $settingGroup . '/' . $key) ?>">
                                        Reset to Default
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="group" value="<?= $settingGroup ?>">
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    // SweetAlert2 for Reset to Default using jQuery
    $(document).ready(function() {
        $('.reset-setting').on('click', function(e) {
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will reset the setting to its default value.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, reset it!',
                cancelButtonText: 'No, cancel',
                reverseButtons: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.get(url)
                        .done(function(data) {
                            Swal.fire({
                                title: 'Success',
                                text: data.message || 'Setting has been reset to default',
                                icon: 'success',
                                showConfirmButton: true
                            }).then(function() {
                                location.reload(); // Reload page
                            });
                        })
                        .fail(function() {
                            Swal.fire({
                                title: 'Failed',
                                text: 'An error occurred while resetting the setting.',
                                icon: 'error',
                                showConfirmButton: true
                            });
                        });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('other-styles') ?>

<?= $this->endSection() ?>