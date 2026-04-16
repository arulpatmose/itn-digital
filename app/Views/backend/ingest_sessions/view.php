<?php /** @var array $session, $chips, $participants */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <!-- Session Info -->
        <div class="col-12 col-lg-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Session Info</h3>
                    <div class="block-options">
                        <a href="<?= base_url('ingest-sessions') ?>" class="btn btn-sm btn-alt-secondary">Back</a>
                    </div>
                </div>
                <div class="block-content pb-3">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Title</dt>
                        <dd class="col-sm-8"><strong><?= esc($session['title']) ?></strong></dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <?php
                            $statusClass = match($session['status']) {
                                'open'    => 'bg-success',
                                'partial' => 'bg-warning text-dark',
                                'closed'  => 'bg-secondary',
                                default   => 'bg-dark',
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>" id="session-status-badge">
                                <?= ucfirst(esc($session['status'])) ?>
                            </span>
                        </dd>

                        <?php if (!empty($session['ingest_location'])): ?>
                        <dt class="col-sm-4">Location</dt>
                        <dd class="col-sm-8"><?= esc($session['ingest_location']) ?></dd>
                        <?php endif; ?>

                        <dt class="col-sm-4">Created By</dt>
                        <dd class="col-sm-8"><?= esc($session['creator_name'] ?? '—') ?></dd>

                        <dt class="col-sm-4">Created</dt>
                        <dd class="col-sm-8"><?= date('d M Y H:i', strtotime($session['created_at'])) ?></dd>

                        <?php if (!empty($session['description'])): ?>
                        <dt class="col-sm-4">Notes</dt>
                        <dd class="col-sm-8 text-muted"><?= esc($session['description']) ?></dd>
                        <?php endif; ?>
                    </dl>

                    <?php if ($session['status'] === 'open' && auth()->user()->can('ingest_sessions.close')): ?>
                    <hr>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-secondary btn-close-session" data-status="closed">Mark Closed</button>
                        <button class="btn btn-sm btn-warning btn-close-session" data-status="partial">Mark Partial</button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chips in session + quick ingest -->
        <div class="col-12 col-lg-8">

            <?php if ($session['status'] === 'open' && auth()->user()->can('transactions.ingest')): ?>
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Quick Ingest</h3>
                </div>
                <div class="block-content pb-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-5">
                            <label class="form-label mb-1">Chips</label>
                            <select class="form-select select2-chips" id="quick-chip-ids" name="chip_ids[]" multiple>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1">From <small class="text-muted">(optional)</small></label>
                            <select class="form-select" id="quick-from-participant">
                                <option value="">— Unknown —</option>
                                <?php foreach ($participants as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label mb-1">Remarks</label>
                            <input type="text" class="form-control" id="quick-remarks" placeholder="Optional">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" id="btn-quick-ingest">
                                <i class="fa fa-layer-group me-1"></i> Ingest Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Chips in this Session</h3>
                </div>
                <div class="block-content block-content-full">
                    <?php if (empty($chips)): ?>
                        <p class="text-muted py-3">No chips ingested yet.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-session-chips">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Chip Code</th>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>Ingested At</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chips as $i => $chip): ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td>
                                            <a href="<?= base_url('chips/detail/' . $chip['id']) ?>">
                                                <strong><?= esc($chip['chip_code']) ?></strong>
                                            </a>
                                        </td>
                                        <td>
                                            <?php
                                            $typeClass = match($chip['chip_type']) {
                                                'SXS'     => 'bg-primary',
                                                'SD'      => 'bg-info',
                                                'MicroSD' => 'bg-warning text-dark',
                                                default   => 'bg-secondary',
                                            };
                                            ?>
                                            <span class="badge <?= $typeClass ?>"><?= esc($chip['chip_type']) ?></span>
                                        </td>
                                        <td><?= $chip['from_name'] ? esc($chip['from_name']) : '<span class="text-muted">—</span>' ?></td>
                                        <td class="text-nowrap"><?= date('d M Y H:i', strtotime($chip['ingested_at'])) ?></td>
                                        <td><?= esc($chip['handler_name'] ?? '—') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
$(function () {
    <?php if ($flash = session()->getFlashdata('success')): ?>
    toast.fire({ icon: 'success', title: <?= json_encode($flash) ?> });
    <?php endif; ?>
    <?php if ($flash = session()->getFlashdata('error')): ?>
    Swal.fire({ icon: 'error', title: 'Error', text: <?= json_encode($flash) ?>, confirmButtonColor: '#dc3545' });
    <?php endif; ?>

    if ($('#table-session-chips').length) {
        $('#table-session-chips').DataTable({
            pagingType: 'full_numbers',
            pageLength: 25,
            order: [[4, 'desc']],
            autoWidth: false,
            responsive: true,
        });
    }

    // Select2 chip picker
    $('#quick-chip-ids').select2({
        placeholder: 'Search chips…',
        minimumInputLength: 1,
        ajax: {
            url: '<?= base_url('chips/api-list') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) { return { results: data }; },
        },
    });

    // Quick ingest submit
    $('#btn-quick-ingest').on('click', function () {
        var chipIds = $('#quick-chip-ids').val();
        if (!chipIds || chipIds.length === 0) {
            toast.fire({ icon: 'warning', title: 'Select at least one chip.' });
            return;
        }
        var $btn = $(this).prop('disabled', true).text('Ingesting…');

        $.ajax({
            url: '<?= base_url('ingest-sessions/' . $session['id'] . '/ingest-chips') ?>',
            type: 'POST',
            data: {
                chip_ids: chipIds,
                from_participant_id: $('#quick-from-participant').val(),
                remarks: $('#quick-remarks').val(),
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Ingest Selected');
                if (res.status === 'success') {
                    toast.fire({ icon: 'success', title: res.message });
                    setTimeout(function () { location.reload(); }, 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
                if (res.warnings && res.warnings.length) {
                    toast.fire({ icon: 'warning', title: res.warnings.join(' ') });
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Ingest Selected');
                toast.fire({ icon: 'error', title: 'An error occurred.' });
            }
        });
    });

    // Close session
    $('.btn-close-session').on('click', function () {
        var status = $(this).data('status');
        var label  = status === 'closed' ? 'Close' : 'Mark as Partial';
        Swal.fire({
            icon: 'question',
            title: label + ' this session?',
            showCancelButton: true,
            confirmButtonText: 'Yes, ' + label.toLowerCase(),
            confirmButtonColor: status === 'closed' ? '#6c757d' : '#e08500',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '<?= base_url('ingest-sessions/' . $session['id'] . '/close') ?>',
                type: 'POST',
                data: { status: status },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        toast.fire({ icon: 'success', title: res.message });
                        $('.btn-close-session').hide();
                        $('#quick-ingest-block').hide();
                        var badgeClass = status === 'closed' ? 'bg-secondary' : 'bg-warning text-dark';
                        $('#session-status-badge').removeClass().addClass('badge ' + badgeClass).text(status.charAt(0).toUpperCase() + status.slice(1));
                    } else {
                        toast.fire({ icon: 'error', title: res.message });
                    }
                },
                error: function () { toast.fire({ icon: 'error', title: 'An error occurred.' }); }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
