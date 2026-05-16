<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">All Roles</h3>
            <div class="block-options">
                <button type="button" class="btn btn-sm btn-warning" id="btn-sync-matrix">
                    <i class="fa fa-sync fa-sm me-1"></i>Sync from Config
                </button>
            </div>
        </div>
        <div class="block-content p-0">
            <table class="table table-hover table-vcenter mb-0">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Description</th>
                        <th class="text-center">Users</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $slug => $info): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($info['title']) ?></div>
                                <div class="fs-xs text-muted font-monospace"><?= esc($slug) ?></div>
                            </td>
                            <td class="text-muted small"><?= esc($info['description']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?= (int) ($userCounts[$slug] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($slug === 'superadmin'): ?>
                                    <a href="<?= base_url('roles/' . $slug) ?>" class="btn btn-sm btn-success">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('roles/' . $slug) ?>" class="btn btn-sm btn-primary">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
$('#btn-sync-matrix').on('click', function() {
    Swal.fire({
        title: 'Sync permissions from config?',
        text: 'This will add any permissions from AuthGroups config that are missing in the database matrix. Existing grants will not be removed.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Sync',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (!result.isConfirmed) return;
        var $btn = $('#btn-sync-matrix');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Syncing…');
        var restore = function() { $btn.prop('disabled', false).html('<i class="fa fa-sync fa-sm me-1"></i>Sync from Config'); };
        $.post(_AppUri + '/roles/sync', {})
            .done(function(res) {
                restore();
                if (res.status === 'success') {
                    var lines = res.added.length
                        ? '<ul class="text-start mb-0 mt-2">' + res.added.map(function(s) { return '<li>' + s + '</li>'; }).join('') + '</ul>'
                        : '';
                    var msg = res.added.length
                        ? res.added.length + ' role(s) updated.' + lines
                        : 'Already in sync — no changes needed.';
                    Swal.fire({ icon: 'success', title: 'Sync complete', html: msg });
                } else {
                    toast.fire({ icon: 'error', title: res.message || 'Sync failed.' });
                }
            })
            .fail(function() {
                restore();
                toast.fire({ icon: 'error', title: 'Request failed. Please try again.' });
            });
    });
});
</script>
<?= $this->endSection() ?>
