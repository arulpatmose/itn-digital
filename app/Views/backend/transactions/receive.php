<?php

/** @var array $sources, $currentParticipant */ ?>

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
                                <p class="text-muted mb-4">Record chips coming in from a librarian or producer.</p>

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
                                    <label class="form-label" for="from_participant_id">Received From <span class="text-danger">*</span></label>
                                    <select class="form-select" id="from_participant_id" name="from_participant_id" required>
                                        <option value="">— Select source —</option>
                                        <?php foreach ($sources as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= old('from_participant_id') == $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['name']) ?> (<?= esc($p['type']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Receiver (You)</label>
                                    <?php if ($currentParticipant): ?>
                                        <input type="hidden" name="to_participant_id" value="<?= $currentParticipant['id'] ?>">
                                        <div class="form-control bg-success-light text-success"><?= esc($currentParticipant['name']) ?> <span class="text-muted">(<?= esc($currentParticipant['type']) ?>)</span></div>
                                    <?php else: ?>
                                        <div class="alert alert-warning mb-0">Your account is not linked to a participant. The receiver will not be recorded.</div>
                                    <?php endif; ?>
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