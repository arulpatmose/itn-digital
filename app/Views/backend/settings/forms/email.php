<?php

/**
 * email
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
    <div class="row item-push">
        <div class="col-md-12">
            <form class="form-horizontal form-submit-event" action="<?= base_url('settings/update') ?>" method="POST" id="settings-form">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Email Settings</h3>
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
                                <div class="col-md-6">
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

        <!-- ── Send Test Email ───────────────────────────── -->

        <div class="col-md-12">
            <div class="block block-rounded">

                <div class="block-header block-header-default">
                    <h3 class="block-title">Send Test Email</h3>
                </div>

                <div class="block-content">
                    <p class="text-muted small mb-3">
                        Send a test email using the current SMTP configuration above to verify delivery is working correctly.
                    </p>

                    <div class="row g-3 mb-4 align-items-end">
                        <div class="col-md-6">
                            <label for="test_email_address" class="col-form-label">Recipient Email Address</label>
                            <input type="email"
                                id="test_email_address"
                                class="form-control"
                                placeholder="admin@example.com">
                        </div>

                        <div class="col-md-3">
                            <button type="button" id="btn-send-test-email" class="btn btn-primary w-100">
                                <i class="fa fa-paper-plane me-1"></i> Send
                            </button>
                        </div>
                    </div>

                    <div id="test-email-result" class="mt-3" style="display:none;"></div>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    // 🔹 Send Test Email
    $('#btn-send-test-email').on('click', function() {
        const email = $('#test_email_address').val().trim();
        const $btn = $(this);
        const $result = $('#test-email-result');

        if (!email) {
            $result.html('<div class="alert alert-warning">Please enter a recipient email address.</div>').show();
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Sending…');
        $result.hide();

        $.ajax({
            url: '<?= base_url('settings/email-test') ?>',
            type: 'POST',
            data: {
                test_email: email,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $result.html('<div class="alert alert-success">' + res.message + '</div>').show();
                } else {
                    $result.html('<div class="alert alert-danger">' + res.message + '</div>').show();
                }
            },
            error: function() {
                $result.html('<div class="alert alert-danger">Request failed. Please try again.</div>').show();
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane me-2"></i> Send Test Email');
            }
        });
    });

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