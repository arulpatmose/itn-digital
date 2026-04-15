<?php

/**
 * view_activity_log
 *
 * Author: Arul Patmose
 *
 * Load Default Global Template and Extend
 *
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        Activity Log
                    </h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100" id="table-activity-log">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Date / Time</th>
                                    <th>Action</th>
                                    <th>Type</th>
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
    jQuery(document).ready(function() {
        if (jQuery('#table-activity-log').length) {
            jQuery('#table-activity-log').DataTable({
                ajax: {
                    url: '/api/get-all-activity-logs',
                    data: function(data) {
                        return {
                            data: data
                        };
                    },
                    dataSrc: function(data) {
                        return data.aaData;
                    },
                    method: 'post'
                },
                serverSide: true,
                processing: true,
                order: [
                    [1, 'desc']
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
                info: true,
                columns: [{
                        data: 'id'
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
                        data: 'actor'
                    }
                ],
                columnDefs: [{
                        targets: [0],
                        width: '4%',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1 + meta.settings._iDisplayStart;
                        }
                    },
                    {
                        targets: [1],
                        width: '14%',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">—</span>';
                            var d = new Date(data.replace(' ', 'T'));
                            return '<span class="fs-sm">' + d.toLocaleDateString() + '<br><small class="text-muted">' + d.toLocaleTimeString() + '</small></span>';
                        }
                    },
                    {
                        targets: [2],
                        width: '14%',
                        render: function(data) {
                            if (!data) return '';
                            var parts = data.split('.');
                            var colorMap = {
                                'created': 'success',
                                'updated': 'info',
                                'deleted': 'danger',
                                'restored': 'secondary',
                                'banned': 'warning',
                                'unbanned': 'warning',
                                'password_changed': 'dark',
                                'password_changed_by_admin': 'dark',
                                'profile_updated': 'info',
                                'groups_updated': 'info',
                            };
                            var verb = parts[1] || 'action';
                            var color = colorMap[verb] || 'primary';
                            return '<span class="badge bg-' + color + ' text-white fs-xs fw-semibold">' + data + '</span>';
                        }
                    },
                    {
                        targets: [3],
                        width: '8%',
                        orderable: false,
                        render: function(data) {
                            if (!data) return '<span class="text-muted">—</span>';
                            return '<span class="fs-xs fw-semibold text-uppercase">' + data + '</span>';
                        }
                    },
                    {
                        targets: [4],
                        orderable: false,
                        render: function(data) {
                            return data ? esc(data) : '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        targets: [5],
                        width: '10%',
                        orderable: false,
                        render: function(data) {
                            return data ? '<code class="fs-xs">' + data + '</code>' : '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        targets: [6],
                        width: '10%',
                        orderable: false,
                        render: function(data) {
                            return data ? data : '<span class="text-muted">System</span>';
                        }
                    }
                ]
            });
        }
    });

    function esc(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }
</script>
<?= $this->endSection() ?>