<?php

/** @var array $producers */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-6 col-xl-6">
            <form action="<?= base_url('transactions/receive') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Receive Chips</h3>
                        <div class="block-options">
                            <a href="<?= base_url('transactions') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-success">Record Receive</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-muted mb-4">Record chips arriving at <strong>ITN Digital</strong> from a producer.</p>

                                <div class="mb-4">
                                    <label class="form-label" for="chip_ids">Chips <span class="text-danger">*</span></label>
                                    <select class="form-select select2-chips" id="chip_ids" name="chip_ids[]" multiple required>
                                        <?php foreach ((array) old('chip_ids') as $oldId): ?>
                                            <?php if ($oldId = (int) $oldId): ?>
                                                <option value="<?= $oldId ?>" selected><?= $oldId ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Search by chip code. Select all chips being received.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="from_participant_id">Received From <small class="text-muted">(optional)</small></label>
                                    <select class="form-select" id="from_participant_id" name="from_participant_id">
                                        <option value="">— Unknown / Direct —</option>
                                        <?php foreach ($producers as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= old('from_participant_id') == $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Select the producer who handed over the chips, if known.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Destination</label>
                                    <div class="form-control bg-light text-muted">
                                        <i class="fa fa-building fa-fw me-1"></i> ITN Digital
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="remarks">Remarks <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="2"
                                        placeholder="Any notes about this transaction…"><?= old('remarks') ?></textarea>
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
