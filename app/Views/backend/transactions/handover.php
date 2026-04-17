<?php ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-6 col-xl-6">
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
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-muted mb-4">Hand over chips from <strong>ITN Digital</strong> to the <strong>Library</strong>. This closes the chip cycle.</p>

                                <div class="mb-4">
                                    <label class="form-label" for="chip_ids">Chips <span class="text-danger">*</span></label>
                                    <select class="form-select select2-chips" id="chip_ids" name="chip_ids[]" multiple required>
                                    </select>
                                    <div class="form-text">
                                        Search by chip code. <span class="text-muted">Chips currently in the library or in an active ingest session are excluded.</span>
                                        <div class="alert alert-warning py-1 px-2 mb-0 mt-2 small"><i class="fa fa-triangle-exclamation me-1"></i>Can't find a chip? It may be in an open ingest session — close and mark copy status done before handing over.</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">From</label>
                                    <div class="form-control bg-light text-muted">
                                        <i class="fa fa-building fa-fw me-1"></i> ITN Digital
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">To</label>
                                    <div class="form-control bg-light text-muted">
                                        <i class="fa fa-book fa-fw me-1"></i> Library
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="to_librarian_id">Received by <span class="text-danger">*</span></label>
                                    <select class="form-select" id="to_librarian_id" name="to_librarian_id" required>
                                        <option value="">— Select librarian —</option>
                                        <?php foreach ($librarians as $lib): ?>
                                            <option value="<?= $lib['id'] ?>" <?= old('to_librarian_id') == $lib['id'] ? 'selected' : '' ?>>
                                                <?= esc($lib['name']) ?>
                                            </option>
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
