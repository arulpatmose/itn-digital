<?php /** @var array $users */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-xl-6">
            <form action="<?= base_url('participants/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Add Participant</h3>
                        <div class="block-options">
                            <a href="<?= base_url('participants') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row py-sm-3 py-md-4">
                            <div class="col-sm-10 col-md-8">

                                <div class="mb-4">
                                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Full name or organisation name"
                                        value="<?= old('name') ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">— Select type —</option>
                                        <option value="staff"    <?= old('type') === 'staff'    ? 'selected' : '' ?>>Staff</option>
                                        <option value="producer" <?= old('type') === 'producer' ? 'selected' : '' ?>>Producer</option>
                                        <option value="librarian" <?= old('type') === 'librarian' ? 'selected' : '' ?>>Librarian</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="user_id">Linked System User <small class="text-muted">(optional)</small></label>
                                    <select class="form-select" id="user_id" name="user_id">
                                        <option value="">— None —</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= old('user_id') == $u['id'] ? 'selected' : '' ?>>
                                                <?= esc($u['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Link to a system user to tie transactions to an account.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="notes">Notes <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="Any relevant notes…"><?= old('notes') ?></textarea>
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
