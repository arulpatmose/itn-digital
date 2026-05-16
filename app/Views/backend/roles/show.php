<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<?php
$moduleLabels = [
    'admin'                  => ['label' => 'Admin & Core',          'icon' => 'fa-shield-alt'],
    'dashboard'              => ['label' => 'Dashboard',             'icon' => 'fa-tachometer-alt'],
    'beta'                   => ['label' => 'Beta Features',         'icon' => 'fa-flask'],
    'users'                  => ['label' => 'Users',                 'icon' => 'fa-users'],
    'clients'                => ['label' => 'Clients',               'icon' => 'fa-building'],
    'commercials'            => ['label' => 'Commercials',           'icon' => 'fa-film'],
    'formats'                => ['label' => 'Ad Formats',            'icon' => 'fa-ad'],
    'platforms'              => ['label' => 'Platforms',             'icon' => 'fa-tv'],
    'spots'                  => ['label' => 'Ad Spots',              'icon' => 'fa-map-marker-alt'],
    'programs'               => ['label' => 'Programs',              'icon' => 'fa-play-circle'],
    'schedule'               => ['label' => 'Schedule',              'icon' => 'fa-calendar-alt'],
    'schedules'              => ['label' => 'Schedules (Bulk)',      'icon' => 'fa-calendar-week'],
    'daily_schedule'         => ['label' => 'Daily Schedule',        'icon' => 'fa-calendar-day'],
    'accounts'               => ['label' => 'Accounts',              'icon' => 'fa-file-invoice'],
    'booking'                => ['label' => 'Bookings',              'icon' => 'fa-bookmark'],
    'booking_purpose_group'  => ['label' => 'Booking Purpose Groups','icon' => 'fa-layer-group'],
    'booking_purpose'        => ['label' => 'Booking Purposes',      'icon' => 'fa-tags'],
    'resource'               => ['label' => 'Resources',             'icon' => 'fa-box'],
    'resource_type'          => ['label' => 'Resource Types',        'icon' => 'fa-cubes'],
    'chips'                  => ['label' => 'Chips',                 'icon' => 'fa-microchip'],
    'participants'           => ['label' => 'Participants',          'icon' => 'fa-user-friends'],
    'transactions'           => ['label' => 'Transactions',          'icon' => 'fa-exchange-alt'],
    'ingest_sessions'        => ['label' => 'Ingest Sessions',       'icon' => 'fa-upload'],
    'ingest'                 => ['label' => 'Ingest Reports',        'icon' => 'fa-chart-bar'],
    'activity_log'           => ['label' => 'Activity Log',          'icon' => 'fa-history'],
];
?>
<div class="content">
    <form action="<?= base_url('roles/' . $group . '/update') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <?= esc($groupDef['title']) ?>
                    <?php if ($isLocked): ?>
                        <span class="badge bg-secondary ms-2 fs-xs">Locked</span>
                    <?php endif; ?>
                </h3>
                <div class="block-options">
                    <a href="<?= base_url('roles') ?>" class="btn btn-sm btn-secondary me-1">Back</a>
                    <?php if (!$isLocked): ?>
                        <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="block-content">
                <?php if ($isLocked): ?>
                <div class="alert alert-info mt-3 mb-0">
                    Superadmin has unrestricted access to all permissions and cannot be edited.
                </div>
                <?php endif; ?>
                <div class="row g-3 py-3">
                    <?php foreach ($modules as $moduleKey => $perms): ?>
                    <?php $mod = $moduleLabels[$moduleKey] ?? ['label' => ucfirst(str_replace('_', ' ', $moduleKey)), 'icon' => 'fa-circle']; ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="block block-rounded mb-0 h-100">
                            <div class="block-header block-header-default py-2 px-3">
                                <h6 class="block-title mb-0">
                                    <i class="fa <?= $mod['icon'] ?> me-1 text-muted"></i>
                                    <?= esc($mod['label']) ?>
                                </h6>
                            </div>
                            <div class="block-content py-2 px-3">
                                <?php foreach ($perms as $perm): ?>
                                <div class="form-check mb-1">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="permissions[]"
                                        id="perm-<?= str_replace('.', '-', $perm['key']) ?>"
                                        value="<?= esc($perm['key']) ?>"
                                        <?= $perm['granted'] ? 'checked' : '' ?>
                                        <?= $isLocked ? 'disabled' : '' ?>
                                    >
                                    <label class="form-check-label small" for="perm-<?= str_replace('.', '-', $perm['key']) ?>">
                                        <?= esc($perm['desc']) ?>
                                        <code class="text-muted fs-xs ms-1"><?= esc($perm['key']) ?></code>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
