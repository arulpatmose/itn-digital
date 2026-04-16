<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-xl-6">
            <form action="<?= base_url('chips/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Register Chip</h3>
                        <div class="block-options">
                            <a href="<?= base_url('chips') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row py-sm-3 py-md-4">
                            <div class="col-sm-10 col-md-8">

                                <div class="mb-4">
                                    <label class="form-label" for="chip_type">Chip Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="chip_type" name="chip_type" required>
                                        <option value="">— Select type —</option>
                                        <option value="SXS"     <?= old('chip_type') === 'SXS'     ? 'selected' : '' ?>>SXS</option>
                                        <option value="SD"      <?= old('chip_type') === 'SD'      ? 'selected' : '' ?>>SD</option>
                                        <option value="MicroSD" <?= old('chip_type') === 'MicroSD' ? 'selected' : '' ?>>MicroSD</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="chip_code">Chip Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" id="chip_code" name="chip_code"
                                        placeholder="e.g. SXS-001"
                                        value="<?= old('chip_code') ?>" required>
                                    <div class="form-text">Will be stored in uppercase automatically.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="notes">Notes <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="Any notes about this chip…"><?= old('notes') ?></textarea>
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
