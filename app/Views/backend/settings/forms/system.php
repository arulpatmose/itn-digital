<?php

/**
 * system
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
                        <h3 class="block-title">General System Settings</h3>
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
                        <div class="row push g-4">
                            <div class="col-md-12">
                                <label for="youtube-data-google-api" class="col-form-label">YouTube Data Google API Key</label>
                                <input type="text" class="form-control" name="youtube-data-google-api" id="youtube-data-google-api"
                                    value="<?= isset($system_settings['youtube-data-google-api']) ? esc($system_settings['youtube-data-google-api']) : '' ?>"
                                    placeholder="Enter YouTube Data Google API Key" autocomplete="off" />
                            </div>

                            <div class="col-md-6">
                                <label for="captcha-sitekey" class="col-form-label">Google reCaptcha Site Key</label>
                                <input type="text" class="form-control" name="captcha-sitekey" id="captcha-sitekey"
                                    value="<?= isset($system_settings['captcha-sitekey']) ? esc($system_settings['captcha-sitekey']) : '' ?>"
                                    placeholder="Enter Captcha Site Key" autocomplete="off" />
                            </div>

                            <div class="col-md-6">
                                <label for="captcha-secret" class="col-form-label">Google reCaptcha Secret</label>
                                <input type="text" class="form-control" name="captcha-secret" id="captcha-secret"
                                    value="<?= isset($system_settings['captcha-secret']) ? esc($system_settings['captcha-secret']) : '' ?>"
                                    placeholder="Enter Captcha Secret" autocomplete="off" />
                            </div>

                            <input type="hidden" name="group" value="<?= $setting_group ?>">

                            <div class="col-md-12 mt-0">
                                <small class="text-muted d-block mt-3">
                                    To use Google reCAPTCHA v3, register the site and enter the Site Key and Secret above.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>

<?= $this->endSection() ?>

<?= $this->section('other-styles') ?>

<?= $this->endSection() ?>