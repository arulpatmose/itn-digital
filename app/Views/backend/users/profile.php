<?php

/**
 * profile
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
        <form class="js-validation-signin" action="<?php echo site_url('users/update-profile'); ?>" method="POST">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">User Information</h3>
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
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label class="form-label" for="schedule-budget">First Name</label>
                                <input type="text" class="form-control" id="first-name" name="first_name"
                                    inputmode="text" placeholder="First Name" value="<?= $user->first_name; ?>"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="schedule-budget">Last Name</label>
                                <input type="text" class="form-control" id="last-name" name="last_name" inputmode="text"
                                    placeholder="Last Name" value="<?= $user->last_name; ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="schedule-budget">Email</label>
                                <input type="text" class="form-control" id="email" name="email" inputmode="email"
                                    placeholder="<?= lang('Auth.email') ?>" value="<?= $user->email; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <form class="js-validation-signin" action="<?php echo site_url('users/update-password'); ?>" method="POST">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Change Password</h3>
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
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label class="form-label" for="schedule-budget">Current Password</label>
                                <input type="password" class="form-control" id="current-password"
                                    name="current-password" inputmode="text" placeholder="Current Password" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="schedule-budget">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    inputmode="text" placeholder="<?= lang('Auth.password') ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="schedule-budget">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm-password"
                                    name="password_confirm" inputmode="text"
                                    placeholder="<?= lang('Auth.passwordConfirm') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>