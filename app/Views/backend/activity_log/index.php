<?php

/** @var array $scope */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Activity Log</h3>
                    <?php if ($scope['user_only']): ?>
                        <div class="block-options">
                            <span class="badge bg-info">Your activity only</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Filters -->
                <div class="block-content border-bottom">
                    <div class="row g-2 align-items-end pb-3">
                        <?php if (!$scope['user_only']): ?>
                            <div class="col-sm-6 col-md-2">
                                <label class="form-label mb-1 fs-sm">User</label>
                                <input type="text" class="form-control form-control-sm" id="filter-user" placeholder="Name or username…">
                            </div>
                        <?php endif; ?>

                        <div class="col-sm-6 col-md-<?= $scope['user_only'] ? '4' : '2' ?>">
                            <label class="form-label mb-1 fs-sm">Action</label>
                            <input type="text" class="form-control form-control-sm" id="filter-action" placeholder="e.g. booking.created…" list="action-suggestions">
                            <datalist id="action-suggestions">
                                <option value="booking.created">
                                <option value="booking.approved">
                                <option value="booking.rejected">
                                <option value="booking.cancelled">
                                <option value="chip.created">
                                <option value="chip.deleted">
                                <option value="ingest_session.created">
                                <option value="ingest_session.closed">
                                <option value="ingest_session.resumed">
                                <option value="user.created">
                                <option value="user.updated">
                                <option value="user.deleted">
                            </datalist>
                        </div>

                        <div class="col-sm-6 col-md-<?= $scope['user_only'] ? '4' : '2' ?>">
                            <label class="form-label mb-1 fs-sm">Target Type</label>
                            <select class="form-select form-select-sm" id="filter-target">
                                <option value="">All types</option>
                                <option value="user">User</option>
                                <option value="booking">Booking</option>
                                <option value="resource">Resource</option>
                                <option value="resource_type">Resource Type</option>
                                <option value="booking_purpose">Booking Purpose</option>
                                <option value="booking_purpose_group">Purpose Group</option>
                                <option value="chip">Chip</option>
                                <option value="ingest_session">Ingest Session</option>
                                <option value="chip_transaction">Transaction</option>
                                <option value="participant">Participant</option>
                                <option value="client">Client</option>
                                <option value="commercial">Commercial</option>
                                <option value="program">Program</option>
                                <option value="schedule">Schedule</option>
                            </select>
                        </div>

                        <div class="col-sm-6 col-md-2">
                            <label class="form-label mb-1 fs-sm">From</label>
                            <input type="date" class="form-control form-control-sm" id="filter-from">
                        </div>

                        <div class="col-sm-6 col-md-2">
                            <label class="form-label mb-1 fs-sm">To</label>
                            <input type="date" class="form-control form-control-sm" id="filter-to">
                        </div>

                        <div class="col-sm-6 col-md-2 d-flex align-items-end">
                            <button class="btn btn-sm btn-alt-secondary w-100" id="btn-reset-filters" title="Reset filters">
                                <i class="fa fa-rotate-left"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-activity-log">
                            <thead>
                                <tr>
                                    <th class="text-center noOrder">#</th>
                                    <th>Date / Time</th>
                                    <th>Action</th>
                                    <th>Target</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    $(function() {
        var table = $('#table-activity-log').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: '<?= base_url('activity-log/get-logs') ?>',
                type: 'POST',
                data: function(d) {
                    d.filter_user = $('#filter-user').val();
                    d.filter_action = $('#filter-action').val();
                    d.filter_target = $('#filter-target').val();
                    d.filter_from = $('#filter-from').val();
                    d.filter_to = $('#filter-to').val();
                    return d;
                }
            },
            order: [
                [0, 'desc']
            ],
            pagingType: 'full_numbers',
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            autoWidth: false,
            responsive: true,
            stateSave: false,
            columns: [{
                    data: 'created_at'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'action'
                },
                {
                    data: 'target_type'
                },
                {
                    data: 'description'
                },
                {
                    data: 'ip_address'
                },
                {
                    data: 'user_name'
                },
            ],
            columnDefs: [{
                    targets: 0,
                    width: '4%',
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                {
                    targets: 1,
                    width: '13%',
                    render: function(data) {
                        if (!data) return '<span class="text-muted">—</span>';
                        var d = new Date(data.replace(' ', 'T'));
                        return '<span class="fs-sm">' + d.toLocaleDateString() + '<br><small class="text-muted">' + d.toLocaleTimeString() + '</small></span>';
                    }
                },
                {
                    targets: 2,
                    width: '15%',
                    render: function(data) {
                        if (!data) return '';
                        var parts = data.split('.');
                        var colorMap = {
                            'created': 'success',
                            'updated': 'info',
                            'deleted': 'danger',
                            'restored': 'secondary',
                            'approved': 'success',
                            'rejected': 'danger',
                            'cancelled': 'warning',
                            'closed': 'secondary',
                            'resumed': 'info',
                            'received': 'primary',
                            'ingested': 'primary',
                            'transferred': 'primary',
                            'handed_over': 'primary',
                            'banned': 'warning',
                            'unbanned': 'warning',
                            'password_changed': 'dark',
                            'password_changed_by_admin': 'dark',
                            'profile_updated': 'info',
                            'groups_updated': 'info',
                        };
                        var verb = parts[1] || 'action';
                        var color = colorMap[verb] || 'primary';
                        return '<span class="badge bg-' + color + ' fs-xs fw-semibold">' + escHtml(data) + '</span>';
                    }
                },
                {
                    targets: 3,
                    width: '10%',
                    orderable: false,
                    render: function(data, type, row) {
                        if (!data) return '<span class="text-muted">—</span>';
                        var label = '<span class="fs-xs fw-semibold text-uppercase text-muted">' + escHtml(data) + '</span>';
                        if (row.target_id) {
                            label += ' <small class="text-muted">#' + row.target_id + '</small>';
                        }
                        return label;
                    }
                },
                {
                    targets: 4,
                    orderable: false,
                    render: function(data) {
                        return data ? escHtml(data) : '<span class="text-muted">—</span>';
                    }
                },
                {
                    targets: 5,
                    width: '10%',
                    orderable: false,
                    render: function(data) {
                        return data ? '<code class="fs-xs">' + escHtml(data) + '</code>' : '<span class="text-muted">—</span>';
                    }
                },
                {
                    targets: 6,
                    width: '10%',
                    orderable: false,
                    render: function(data) {
                        return data ? escHtml(data) : '<span class="text-muted">System</span>';
                    }
                }
            ]
        });

        // Debounced filter reload
        var debounceTimer;

        function applyFilters() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                table.ajax.reload();
            }, 450);
        }

        $('#filter-user, #filter-action').on('keyup', applyFilters);
        $('#filter-target, #filter-from, #filter-to').on('change', function() {
            table.ajax.reload();
        });

        $('#btn-reset-filters').on('click', function() {
            $('#filter-user, #filter-action, #filter-from, #filter-to').val('');
            $('#filter-target').val('');
            table.ajax.reload();
        });

        function escHtml(str) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str));
            return d.innerHTML;
        }
    });
</script>
<?= $this->endSection() ?>