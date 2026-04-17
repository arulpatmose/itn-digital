<?php

/** @var array $producers, $currentParticipant */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-6 col-xl-6">
            <form action="<?= base_url('transactions/transfer') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Transfer Chips</h3>
                        <div class="block-options">
                            <a href="<?= base_url('transactions') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-info">Record Transfer</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-muted mb-4">Transfer chips between producers.</p>

                                <div class="mb-4">
                                    <label class="form-label" for="chip_ids">Chips <span class="text-danger">*</span></label>
                                    <select class="form-select select2-chips" id="chip_ids" name="chip_ids[]" multiple required>
                                    </select>
                                    <div class="form-text">
                                        Search by chip code. <span class="text-muted">Chips currently in the library or in an active ingest session are excluded.</span>
                                        <div class="alert alert-warning py-1 px-2 mb-0 mt-2 small"><i class="fa fa-triangle-exclamation me-1"></i>Can't find a chip? Close any open ingest sessions first, or hand it over from the library if it has already been returned.</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="from_participant_id">From Producer <span class="text-danger">*</span></label>
                                    <select class="form-select" id="from_participant_id" name="from_participant_id" required>
                                        <option value="">— Select producer —</option>
                                        <?php foreach ($producers as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= old('from_participant_id') == $p['id'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="to_participant_id">To Producer <span class="text-danger">*</span></label>
                                    <select class="form-select" id="to_participant_id" name="to_participant_id" required>
                                        <option value="">— Select producer —</option>
                                        <?php foreach ($producers as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= old('to_participant_id') == $p['id'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
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

        function syncProducerDropdowns() {
            var fromVal = $('#from_participant_id').val();
            var toVal   = $('#to_participant_id').val();

            $('#to_participant_id option').prop('disabled', false);
            $('#from_participant_id option').prop('disabled', false);

            if (fromVal) $('#to_participant_id option[value="' + fromVal + '"]').prop('disabled', true);
            if (toVal)   $('#from_participant_id option[value="' + toVal + '"]').prop('disabled', true);

            if ($('#to_participant_id').val() === fromVal) $('#to_participant_id').val('');
            if ($('#from_participant_id').val() === toVal) $('#from_participant_id').val('');
        }

        $('#from_participant_id, #to_participant_id').on('change', syncProducerDropdowns);
        syncProducerDropdowns();

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
                        q: params.term,
                        exclude_open_session: 1,
                        exclude_location: 'library',
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