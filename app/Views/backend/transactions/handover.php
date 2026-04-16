<?php /** @var array $participants */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-10 col-xl-8">
            <form action="<?= base_url('transactions/handover') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Hand Over Chips</h3>
                        <div class="block-options">
                            <a href="<?= base_url('transactions') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-warning">Record Handover</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row py-sm-3 py-md-4">
                            <div class="col-md-10 col-lg-8">
                                <p class="text-muted mb-4">Hand chips to a producer or library for use.</p>

                                <div class="mb-4">
                                    <label class="form-label" for="chip_ids">Chips <span class="text-danger">*</span></label>
                                    <select class="form-select select2-chips" id="chip_ids" name="chip_ids[]" multiple required>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="from_participant_id">From Participant <small class="text-muted">(optional)</small></label>
                                    <select class="form-select" id="from_participant_id" name="from_participant_id">
                                        <option value="">— Unknown / Not applicable —</option>
                                        <?php foreach ($participants as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?> (<?= esc($p['type']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="to_participant_id">Hand Over To <span class="text-danger">*</span></label>
                                    <select class="form-select" id="to_participant_id" name="to_participant_id" required>
                                        <option value="">— Select participant —</option>
                                        <?php foreach ($participants as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?> (<?= esc($p['type']) ?>)</option>
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
$(function () {
    <?php if ($flash = session()->getFlashdata('error')): ?>
    Swal.fire({ icon: 'error', title: 'Error', text: <?= json_encode($flash) ?>, confirmButtonColor: '#dc3545' });
    <?php endif; ?>

    $('.select2-chips').select2({
        placeholder: 'Search by chip code…',
        minimumInputLength: 1,
        ajax: {
            url: '<?= base_url('chips/api-list') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) { return { results: data }; },
        },
    });
});
</script>
<?= $this->endSection() ?>
