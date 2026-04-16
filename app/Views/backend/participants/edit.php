<?php /** @var array $participant, $users */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-xl-6">
            <form action="<?= base_url('participants/update/' . $participant['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Edit Participant — <?= esc($participant['name']) ?></h3>
                        <div class="block-options">
                            <a href="<?= base_url('participants') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row py-sm-3 py-md-4">
                            <div class="col-sm-10 col-md-8">

                                <div class="mb-4">
                                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?= old('name', $participant['name']) ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type" name="type" required>
                                        <?php foreach (['staff', 'producer', 'librarian'] as $t): ?>
                                            <option value="<?= $t ?>" <?= (old('type', $participant['type']) === $t) ? 'selected' : '' ?>>
                                                <?= ucfirst($t) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="user_id">Linked System User <small class="text-muted">(optional)</small></label>
                                    <select class="form-select" id="user_id" name="user_id">
                                        <option value="">— None —</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= (old('user_id', $participant['user_id']) == $u['id']) ? 'selected' : '' ?>>
                                                <?= esc($u['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="notes">Notes <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $participant['notes'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Status</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_active" id="active_yes" value="1"
                                                <?= old('is_active', $participant['is_active']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="active_yes">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_active" id="active_no" value="0"
                                                <?= !old('is_active', $participant['is_active']) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="active_no">Inactive</label>
                                        </div>
                                    </div>
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
