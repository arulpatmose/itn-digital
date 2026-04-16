<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-8 col-xl-6">
            <form action="<?= base_url('ingest-sessions/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">New Ingest Session</h3>
                        <div class="block-options">
                            <a href="<?= base_url('ingest-sessions') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Create Session</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row py-sm-3 py-md-4">
                            <div class="col-sm-10 col-md-8">

                                <div class="mb-4">
                                    <label class="form-label" for="title">Session Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="e.g. Evening News – 16 Apr 2026"
                                        value="<?= old('title') ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="ingest_location">Location <small class="text-muted">(optional)</small></label>
                                    <input type="text" class="form-control" id="ingest_location" name="ingest_location"
                                        placeholder="e.g. Ingest Bay 1"
                                        value="<?= old('ingest_location') ?>">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="description">Description <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        placeholder="Brief description of this session…"><?= old('description') ?></textarea>
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
