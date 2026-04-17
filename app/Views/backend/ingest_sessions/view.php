<?php

/** @var array $session, $chips, $progress */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<?php $isActive = in_array($session['status'], ['open', 'partial']); ?>
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
                            $statusClass = match ($session['status']) {
                                'open'    => 'bg-success',
                                'partial' => 'bg-warning',
                                'closed'  => 'bg-secondary',
                                default   => 'bg-dark',
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>" id="session-status-badge">
                                <?= ucfirst(esc($session['status'])) ?>
                            </span>
                        </dd>

                        <?php if (!empty($session['ingest_location'])): ?>
                            <dt class="col-sm-4">Ingest Path</dt>
                            <dd class="col-sm-8 text-break"><?= esc($session['ingest_location']) ?></dd>
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

                    <?php if ($progress['total'] > 0): ?>
                        <hr>
                        <div class="mb-1 d-flex justify-content-between">
                            <small class="text-muted">Copy progress</small>
                            <small id="progress-label"><strong><?= $progress['done'] ?></strong> / <?= $progress['total'] ?> done</small>
                        </div>
                        <div class="progress" style="height:8px;">
                            <?php $pct = $progress['total'] ? round($progress['done'] / $progress['total'] * 100) : 0; ?>
                            <div class="progress-bar bg-success" id="progress-bar" role="progressbar"
                                style="width:<?= $pct ?>%"></div>
                        </div>
                    <?php endif; ?>

                    <?php if (auth()->user()->can('ingest_sessions.close')): ?>
                        <hr>
                        <?php if ($isActive): ?>
                            <div class="d-flex gap-2 flex-wrap" id="close-buttons">
                                <button class="btn btn-sm btn-secondary btn-close-session" data-status="closed">Mark Closed</button>
                                <button class="btn btn-sm btn-warning btn-close-session" data-status="partial">Mark Partial</button>
                            </div>
                        <?php else: ?>
                            <button class="btn btn-sm btn-warning" id="btn-resume-session">
                                <i class="fa fa-rotate-right me-1"></i> Resume Session
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chips in session + quick ingest -->
        <div class="col-12 col-lg-8">

            <?php if ($isActive && auth()->user()->can('transactions.ingest')): ?>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            Quick Ingest
                            <?php if ($session['status'] === 'partial'): ?>
                                <span class="badge bg-warning ms-2 fs-xs">Resuming</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="block-content pb-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-12">
                                <label class="form-label mb-1">Chips</label>
                                <select class="form-select select2-chips" id="quick-chip-ids" name="chip_ids[]" multiple></select>
                            </div>
                            <div class="col-12 col-md-10">
                                <label class="form-label mb-1">Remarks</label>
                                <input type="text" class="form-control" id="quick-remarks" placeholder="Optional">
                            </div>
                            <div class="col-12 col-md-2">
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
                                        <th class="text-center">Copy Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($chips as $i => $chip): ?>
                                        <tr id="chip-row-<?= $chip['item_id'] ?>">
                                            <td class="text-muted"><?= $i + 1 ?></td>
                                            <td>
                                                <a href="<?= base_url('chips/detail/' . $chip['id']) ?>">
                                                    <strong><?= esc($chip['chip_code']) ?></strong>
                                                </a>
                                            </td>
                                            <td>
                                                <?php
                                                $typeClass = match ($chip['chip_type']) {
                                                    'SXS'     => 'bg-primary',
                                                    'SD'      => 'bg-info',
                                                    'MicroSD' => 'bg-warning',
                                                    default   => 'bg-secondary',
                                                };
                                                ?>
                                                <span class="badge <?= $typeClass ?>"><?= esc($chip['chip_type']) ?></span>
                                            </td>
                                            <td><?= $chip['from_name'] ? esc($chip['from_name']) : '<span class="text-muted">—</span>' ?></td>
                                            <td class="text-nowrap"><?= date('d M Y H:i', strtotime($chip['ingested_at'])) ?></td>
                                            <td><?= esc($chip['handler_name'] ?? '—') ?></td>
                                            <td class="text-center">
                                                <?php if ($chip['copy_status'] === 'done'): ?>
                                                    <span class="badge bg-success me-1">Copied</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary me-1">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($chip['copy_status'] === 'done'): ?>
                                                    <?php if ($isActive && auth()->user()->can('transactions.ingest')): ?>
                                                        <button class="btn btn-sm btn-secondary btn-toggle-status"
                                                            data-item="<?= $chip['item_id'] ?>" data-status="pending"
                                                            title="Mark as pending" data-bs-toggle="tooltip">
                                                            <i class="fa fa-undo"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php if ($isActive && auth()->user()->can('transactions.ingest')): ?>
                                                        <button class="btn btn-sm btn-success btn-toggle-status"
                                                            data-item="<?= $chip['item_id'] ?>" data-status="done"
                                                            title="Mark as copied" data-bs-toggle="tooltip">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
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
    $(function() {
        <?php if ($flash = session()->getFlashdata('success')): ?>
            toast.fire({
                icon: 'success',
                title: <?= json_encode($flash) ?>
            });
        <?php endif; ?>
        <?php if ($flash = session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#dc3545'
            });
        <?php endif; ?>

        if ($('#table-session-chips').length) {
            $('#table-session-chips').DataTable({
                pagingType: 'full_numbers',
                pageLength: 25,
                order: [
                    [4, 'desc']
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: 7
                }],
            });
        }

        $('#quick-chip-ids').select2({
            placeholder: 'Search chips…',
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
                        exclude_session_id: <?= $session['id'] ?>,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
            },
        });

        // ── Copy status toggle ────────────────────────────────────────────────────
        var chipStatusUrl = '<?= base_url('ingest-sessions/' . $session['id'] . '/chip-status') ?>';

        $(document).on('click', '.btn-toggle-status', function() {
            var $btn = $(this);
            var itemId = $btn.data('item');
            var status = $btn.data('status');

            $btn.prop('disabled', true);

            $.post(chipStatusUrl, {
                    item_id: itemId,
                    status: status
                })
                .done(function(res) {
                    var $row = $('#chip-row-' + itemId);
                    var isDone = res.new_status === 'done';

                    // Swap badge + button
                    var badge = isDone ?
                        '<span class="badge bg-success me-1">Copied</span>' :
                        '<span class="badge bg-secondary me-1">Pending</span>';
                    var btnHtml = isDone ?
                        '<button class="btn btn-xs btn-alt-secondary btn-toggle-status" data-item="' + itemId + '" data-status="pending" title="Mark as pending"><i class="fa fa-undo"></i></button>' :
                        '<button class="btn btn-xs btn-alt-success btn-toggle-status" data-item="' + itemId + '" data-status="done" title="Mark as copied"><i class="fa fa-check"></i></button>';
                    $row.find('td:last').html(badge + btnHtml);

                    // Update progress
                    var done = res.progress.done;
                    var total = res.progress.total;
                    var pct = total ? Math.round(done / total * 100) : 0;
                    $('#progress-bar').css('width', pct + '%');
                    $('#progress-label').html('<strong>' + done + '</strong> / ' + total + ' done');

                    // All done prompt
                    if (res.all_done) {
                        Swal.fire({
                            icon: 'success',
                            title: 'All chips copied!',
                            text: 'Every chip in this session has been marked as copied. Close the session now?',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, close session',
                            cancelButtonText: 'Not yet',
                            confirmButtonColor: '#6c757d',
                        }).then(function(result) {
                            if (result.isConfirmed) closeSession('closed');
                        });
                    }
                })
                .fail(function() {
                    toast.fire({
                        icon: 'error',
                        title: 'Failed to update status.'
                    });
                    $btn.prop('disabled', false);
                });
        });

        // ── Quick ingest ──────────────────────────────────────────────────────────
        $('#btn-quick-ingest').on('click', function() {
            var chipIds = $('#quick-chip-ids').val();
            if (!chipIds || chipIds.length === 0) {
                toast.fire({
                    icon: 'warning',
                    title: 'Select at least one chip.'
                });
                return;
            }
            var $btn = $(this).prop('disabled', true).text('Ingesting…');

            $.ajax({
                url: '<?= base_url('ingest-sessions/' . $session['id'] . '/ingest-chips') ?>',
                type: 'POST',
                data: {
                    chip_ids: chipIds,
                    remarks: $('#quick-remarks').val(),
                },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Ingest Selected');
                    if (res.status === 'success') {
                        toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1200);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                    if (res.warnings && res.warnings.length) {
                        toast.fire({
                            icon: 'warning',
                            title: res.warnings.join(' ')
                        });
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="fa fa-layer-group me-1"></i> Ingest Selected');
                    toast.fire({
                        icon: 'error',
                        title: 'An error occurred.'
                    });
                }
            });
        });

        // ── Resume session ────────────────────────────────────────────────────────
        $('#btn-resume-session').on('click', function() {
            Swal.fire({
                icon: 'question',
                title: 'Resume this session?',
                text: 'The session will be set back to open and you can continue ingesting.',
                showCancelButton: true,
                confirmButtonText: 'Yes, resume',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#e08500',
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.post('<?= base_url('ingest-sessions/' . $session['id'] . '/resume') ?>')
                    .done(function(res) {
                        if (res.status === 'success') {
                            toast.fire({
                                icon: 'success',
                                title: res.message
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1200);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message
                            });
                        }
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not resume the session.'
                        });
                    });
            });
        });

        // ── Close session ─────────────────────────────────────────────────────────
        function closeSession(status) {
            $.ajax({
                url: '<?= base_url('ingest-sessions/' . $session['id'] . '/close') ?>',
                type: 'POST',
                data: {
                    status: status
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1200);
                    } else {
                        toast.fire({
                            icon: 'error',
                            title: res.message
                        });
                    }
                },
                error: function() {
                    toast.fire({
                        icon: 'error',
                        title: 'An error occurred.'
                    });
                }
            });
        }

        $('.btn-close-session').on('click', function() {
            var status = $(this).data('status');
            var label = status === 'closed' ? 'Close' : 'Mark as Partial';
            Swal.fire({
                icon: 'question',
                title: label + ' this session?',
                showCancelButton: true,
                confirmButtonText: 'Yes, ' + label.toLowerCase(),
                confirmButtonColor: status === 'closed' ? '#6c757d' : '#e08500',
            }).then(function(result) {
                if (result.isConfirmed) closeSession(status);
            });
        });
    });
</script>
<?= $this->endSection() ?>