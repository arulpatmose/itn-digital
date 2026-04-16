<?php

/**
 * Migration Runner
 * Superadmin only — manage and sync database migrations.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">

    <?php if ($pending_count > 0): ?>
        <!-- Workflow guidance -->
        <div class="alert alert-info" role="alert">
            <h6 class="alert-heading mb-1"><i class="fa fa-info-circle me-1"></i> Out-of-sync database? Follow this order:</h6>
            <ol class="mb-0 mt-2 small">
                <li>Click <strong>Run ▶</strong> on each genuinely new migration (e.g. new feature migrations) to execute them one at a time.</li>
                <li>Once all new migrations are executed, click <strong>Sync History</strong> to bulk-mark any remaining historical migrations (already applied via SQL import) as recorded without re-executing.</li>
                <li>Going forward, use <strong>Run Pending</strong> for normal operation.</li>
            </ol>
        </div>
    <?php endif; ?>

    <!-- Header block -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Migration Runner</h3>
                    <div class="block-options d-flex gap-2">

                        <!-- Sync History: marks all unrecorded migrations as run without executing -->
                        <?php if ($pending_count > 0): ?>
                            <form method="post" action="<?= base_url('migrations/sync') ?>"
                                class="swal-confirm-form"
                                data-swal-title="Sync History"
                                data-swal-html="This will mark all <strong><?= $pending_count ?> pending migration(s)</strong> as already run — <strong>no SQL will be executed</strong>.<br><br>Only do this for migrations already applied via SQL import."
                                data-swal-icon="warning"
                                data-swal-confirm-text="Yes, Sync"
                                data-swal-confirm-color="#f0a92e">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="fa fa-sync me-1"></i> Sync History (<?= $pending_count ?>)
                                </button>
                            </form>
                        <?php endif; ?>

                        <!-- Run All Pending: uses CI4's migration runner — only safe when DB is in sync -->
                        <form method="post" action="<?= base_url('migrations/run') ?>"
                            class="swal-confirm-form"
                            data-swal-title="Run All Pending"
                            data-swal-html="This runs <strong>all pending migrations</strong> via the CI4 runner.<br><br>Only use this when the migrations table is fully in sync. Historical migrations already in the DB will fail."
                            data-swal-icon="question"
                            data-swal-confirm-text="Yes, Run"
                            data-swal-confirm-color="#6c757d">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm <?= $pending_count > 0 ? 'btn-outline-success' : 'btn-success' ?>">
                                <i class="fa fa-play me-1"></i> Run All Pending
                            </button>
                        </form>

                    </div>
                </div>

                <!-- Summary pills -->
                <div class="block-content pb-0">
                    <div class="row g-3 mb-3">
                        <div class="col-auto">
                            <span class="badge bg-success">
                                <?= $recorded_count ?> recorded
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge <?= $pending_count > 0 ? 'bg-warning' : 'bg-secondary' ?>">
                                <?= $pending_count ?> pending
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-info">
                                Highest batch: <?= $max_batch ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Migration table -->
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-content block-content-full">
                    <table class="table table-striped table-vcenter table-sm">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Version</th>
                                <th>Class</th>
                                <th class="text-center" style="width:70px">Batch</th>
                                <th class="text-center" style="width:100px">Status</th>
                                <th class="text-center" style="width:120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($migrations as $i => $m): ?>
                                <tr>
                                    <td class="text-muted"><?= $i + 1 ?></td>
                                    <td><code class="small"><?= esc($m['version']) ?></code></td>
                                    <td class="text-muted small"><?= esc($m['class']) ?></td>
                                    <td class="text-center">
                                        <?php if ($m['batch'] !== null): ?>
                                            <span class="badge bg-secondary"><?= $m['batch'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($m['recorded']): ?>
                                            <span class="badge bg-success">Recorded</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <?php if (! $m['recorded']): ?>
                                                <!-- Run this single migration (executes up()) -->
                                                <form method="post" action="<?= base_url('migrations/run-single') ?>"
                                                    class="swal-confirm-form"
                                                    data-swal-title="Execute Migration"
                                                    data-swal-html="Run <code><?= esc($m['version']) ?></code>?<br><br>This will call <strong>up()</strong> on this migration class."
                                                    data-swal-icon="question"
                                                    data-swal-confirm-text="Yes, Execute"
                                                    data-swal-confirm-color="#28a745">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="version" value="<?= esc($m['version']) ?>">
                                                    <button type="submit" class="btn btn-xs btn-success" data-bs-toggle="tooltip" title="Execute this migration">
                                                        <i class="fa fa-play"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($m['recorded'] && $m['batch'] !== null): ?>
                                                <!-- Rollback to batch BEFORE this one -->
                                                <form method="post" action="<?= base_url('migrations/rollback/' . ($m['batch'] - 1)) ?>"
                                                    class="swal-confirm-form"
                                                    data-swal-title="Rollback Batch <?= $m['batch'] ?>"
                                                    data-swal-html="Roll back <strong>batch <?= $m['batch'] ?></strong>?<br><br>This calls <strong>down()</strong> on all migrations in that batch. This cannot be undone."
                                                    data-swal-icon="warning"
                                                    data-swal-confirm-text="Yes, Rollback"
                                                    data-swal-confirm-color="#dc3545">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-xs btn-danger" data-bs-toggle="tooltip" title="Rollback batch <?= $m['batch'] ?>">
                                                        <i class="fa fa-undo"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    $(function() {

        // Flash messages via SweetAlert2
        <?php if ($flash = session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#28a745',
            });
        <?php endif; ?>

        <?php if ($flash = session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#dc3545',
            });
        <?php endif; ?>

        // Intercept all confirm forms
        $(document).on('submit', '.swal-confirm-form', function(e) {
            e.preventDefault();

            var $form = $(this);
            var title = $form.data('swal-title') || 'Are you sure?';
            var html = $form.data('swal-html') || '';
            var icon = $form.data('swal-icon') || 'warning';
            var btnText = $form.data('swal-confirm-text') || 'Yes, proceed';
            var btnColor = $form.data('swal-confirm-color') || '#3085d6';

            Swal.fire({
                title: title,
                html: html,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: btnColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: btnText,
                cancelButtonText: 'Cancel',
                reverseButtons: true,
            }).then(function(result) {
                if (result.isConfirmed) {
                    $form[0].submit();
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>