<?php
/**
 * Add Resource form.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-10 col-xl-6">
            <form action="<?= site_url('resources/submit') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Add Resource</h3>
                        <div class="block-options">
                            <a href="<?= base_url('resources') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row py-sm-3 py-md-4">
                            <div class="col-sm-10 col-md-8">

                                <div class="mb-4">
                                    <label class="form-label" for="type_id">Resource Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type_id" name="type_id" required>
                                        <option value="">— Select type —</option>
                                        <?php foreach ($resource_types as $type): ?>
                                            <option value="<?= $type['id'] ?>"><?= esc($type['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">
                                        Manage types from <a href="<?= base_url('resource-types') ?>">Resource Types</a>.
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="name">Resource Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="e.g. Studio A, Conference Room 1"
                                        value="<?= old('name') ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="description">Description <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        placeholder="Brief description of the resource…"><?= old('description') ?></textarea>
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
