<?php

/** @var array $preloadChips */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-6 col-xl-6">
            <form action="<?= base_url('transactions/ingest') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Ingest Chips</h3>
                        <div class="block-options">
                            <a href="<?= base_url('transactions') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Record Ingest</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-muted mb-4">A new ingest session will be created for this transaction.</p>

                                <h5 class="mb-3">Session Details</h5>

                                <div class="mb-4">
                                    <label class="form-label" for="session_title">Session Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="session_title" name="session_title"
                                        value="<?= old('session_title') ?>" placeholder="e.g. Programme name, shoot date…" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="ingest_location">Ingest Path <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ingest_location" name="ingest_location"
                                        value="<?= old('ingest_location') ?>" placeholder="e.g. /media/ingest/2026-04-17/…" required>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Chips</h5>

                                <div class="mb-4">
                                    <label class="form-label" for="chip_ids">Chips <span class="text-danger">*</span></label>
                                    <select class="form-select select2-chips" id="chip_ids" name="chip_ids[]" multiple required>
                                        <?php foreach ($preloadChips as $c): ?>
                                            <option value="<?= $c['id'] ?>" selected><?= esc($c['text']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Search by chip code. <span class="text-muted">All registered chips are shown.</span></div>
                                </div>


                                <div class="mb-4">
                                    <label class="form-label" for="remarks">Remarks <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="2"
                                        placeholder="Any notes…"><?= old('remarks') ?></textarea>
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

<?= $this->section('other-scripts') ?>
<script>
    $(function() {
        <?php if ($flash = session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#dc3545'
            });
        <?php endif; ?>
        <?php if ($flash = session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Received!',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#0d6efd',
                timer: 3000,
                timerProgressBar: true
            });
        <?php endif; ?>

        $('.select2-chips').select2({
            placeholder: 'Search by chip code…',
            minimumInputLength: 1,
            dropdownParent: document.querySelector('#page-container'),
            ajax: {
                url: '<?= base_url('chips/api-list') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
            },
        });
    });
</script>
<?= $this->endSection() ?>