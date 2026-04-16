<?php

/** @var array $chip */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-xl-6">
            <form action="<?= base_url('chips/update/' . $chip['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Edit Chip — <?= esc($chip['chip_code']) ?></h3>
                        <div class="block-options">
                            <a href="<?= base_url('chips') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="mb-4">
                                    <label class="form-label" for="chip_type">Chip Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="chip_type" name="chip_type" required>
                                        <option value="">— Select type —</option>
                                        <?php foreach (['SXS', 'SD', 'MicroSD'] as $t): ?>
                                            <option value="<?= $t ?>" <?= (old('chip_type', $chip['chip_type']) === $t) ? 'selected' : '' ?>><?= $t ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="chip_code">Chip Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" id="chip_code" name="chip_code"
                                        value="<?= old('chip_code', $chip['chip_code']) ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="notes">Notes <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $chip['notes'] ?? '') ?></textarea>
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