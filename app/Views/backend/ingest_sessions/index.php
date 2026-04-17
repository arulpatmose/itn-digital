<?php

/** @var array $sessions */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Ingest Sessions</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('ingest_sessions.create')): ?>
                            <a href="<?= base_url('transactions/ingest') ?>" class="btn btn-sm btn-primary">New Session</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">

                    <!-- Filters -->
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-session-status">
                                    Status
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearSessionFilter('filter-session-status')">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-session-status" style="width:100%;" data-placeholder="All Statuses">
                                    <option></option>
                                    <option value="open">Open</option>
                                    <option value="partial">Partial</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-session-creator">
                                    Created By
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearSessionFilter('filter-session-creator')">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-session-creator" style="width:100%;" data-placeholder="All Users">
                                    <option></option>
                                    <?php
                                    $creators = array_unique(array_filter(array_column($sessions, 'creator_name')));
                                    sort($creators);
                                    foreach ($creators as $c): ?>
                                        <option value="<?= esc($c) ?>"><?= esc($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    Search
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="$('#filter-session-search').val(''); sessionTable.search('').draw();">Reset</button>
                                </label>
                                <input type="text" class="form-control" id="filter-session-search" placeholder="Search title, path, user…">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-sessions">
                            <thead>
                                <tr>
                                    <th class="text-center col-si-index">#</th>
                                    <th class="col-si-title">Title</th>
                                    <th class="col-si-path text-start">Ingest Path</th>
                                    <th class="col-si-status">Status</th>
                                    <th class="col-si-creator">Ingested By</th>
                                    <th class="col-si-date">Created At</th>
                                    <th class="text-center col-si-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $i => $s): ?>
                                    <tr data-status="<?= esc($s['status']) ?>" data-creator="<?= esc($s['creator_name'] ?? '') ?>">
                                        <td class="text-muted text-center"><?= $i + 1 ?></td>
                                        <td><strong><?= esc($s['title']) ?></strong>
                                            <?php if (!empty($s['description'])): ?>
                                                <br><small class="text-muted"><?= esc($s['description']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-start"><?= esc($s['ingest_location'] ?? '—') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = match ($s['status']) {
                                                'open'    => 'bg-success',
                                                'partial' => 'bg-warning',
                                                'closed'  => 'bg-secondary',
                                                default   => 'bg-dark',
                                            };
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= ucfirst(esc($s['status'])) ?></span>
                                        </td>
                                        <td><?= esc($s['creator_name'] ?? '—') ?></td>
                                        <td class="text-nowrap"><?= date('d M Y H:i', strtotime($s['created_at'])) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">

                                                <!-- View -->
                                                <a href="<?= base_url('ingest-sessions/' . $s['id']) ?>"
                                                    class="btn btn-secondary" title="View"
                                                    data-bs-toggle="tooltip">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                <!-- Close -->
                                                <?php if ($s['status'] === 'open' && auth()->user()->can('ingest_sessions.close')): ?>
                                                    <button class="btn btn-danger btn-close-session-row"
                                                        data-id="<?= $s['id'] ?>"
                                                        data-title="<?= esc($s['title']) ?>"
                                                        title="Close session" data-bs-toggle="tooltip">
                                                        <i class="fa fa-lock"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Resume -->
                                                <?php if (in_array($s['status'], ['closed', 'partial']) && auth()->user()->can('ingest_sessions.close')): ?>
                                                    <button class="btn btn-success btn-resume-session"
                                                        data-id="<?= $s['id'] ?>"
                                                        data-title="<?= esc($s['title']) ?>"
                                                        data-status="<?= $s['status'] ?>"
                                                        title="Resume session" data-bs-toggle="tooltip">
                                                        <i class="fa fa-rotate-right"></i>
                                                    </button>
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
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    $(function() {
        <?php if ($flash = session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#28a745'
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

        // Close session from table
        $(document).on('click', '.btn-close-session-row', function() {
            var id = $(this).data('id');
            var title = $(this).data('title');

            Swal.fire({
                icon: 'question',
                title: 'Close session?',
                html: '<strong>' + $('<div>').text(title).html() + '</strong>',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Mark Closed',
                denyButtonText: 'Mark Partial',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#6c757d',
                denyButtonColor: '#e08500',
            }).then(function(result) {
                if (!result.isConfirmed && !result.isDenied) return;
                var status = result.isConfirmed ? 'closed' : 'partial';

                $.post('<?= base_url('ingest-sessions/') ?>' + id + '/close', {
                        status: status
                    })
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
                            text: 'Could not close the session.'
                        });
                    });
            });
        });

        // Resume session
        $(document).on('click', '.btn-resume-session', function() {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var status = $(this).data('status');
            var label = status === 'closed' ? 'closed' : 'partially closed';

            Swal.fire({
                icon: 'question',
                title: 'Resume session?',
                html: '<strong>' + $('<div>').text(title).html() + '</strong> is ' + label + '.<br>Reopen it to continue ingesting?',
                showCancelButton: true,
                confirmButtonText: 'Yes, resume',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#e08500',
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $.post('<?= base_url('ingest-sessions/') ?>' + id + '/resume')
                    .done(function(res) {
                        if (res.status === 'success') {
                            toast.fire({
                                icon: 'success',
                                title: res.message
                            });
                            setTimeout(function() {
                                window.location.href = '<?= base_url('ingest-sessions/') ?>' + id;
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

        ['#filter-session-status', '#filter-session-creator'].forEach(function(sel) {
            $(sel).select2({
                placeholder: $(sel).data('placeholder') || 'All',
                dropdownParent: document.querySelector('#page-container')
            });
        });

        function clearSessionFilter(id) {
            $('#' + id).val(null).trigger('change');
        }

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'table-sessions') return true;
            var row = $(settings.aoData[dataIndex].nTr);
            var status = $('#filter-session-status').val();
            var creator = $('#filter-session-creator').val();
            if (status && row.data('status') !== status) return false;
            if (creator && row.data('creator') !== creator) return false;
            return true;
        });

        $('#filter-session-search').on('input', function() {
            sessionTable.search(this.value).draw();
        });

        var sessionTable = $('#table-sessions').DataTable({
            dom: 'lrtip',
            pagingType: 'full_numbers',
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            autoWidth: true,
            scrollX: true,
            stateSave: true,
            order: [
                [5, 'desc']
            ],
            columnDefs: [{
                    targets: 'col-si-index',
                    width: '4%',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    targets: 'col-si-title',
                    width: '25%'
                },
                {
                    targets: 'col-si-path',
                    width: '22%'
                },
                {
                    targets: 'col-si-status',
                    width: '8%'
                },
                {
                    targets: 'col-si-creator',
                    width: '15%'
                },
                {
                    targets: 'col-si-date',
                    width: '13%'
                },
                {
                    targets: 'col-si-actions',
                    width: '13%',
                    orderable: false,
                    className: 'text-center'
                },
            ],
            drawCallback: function() {
                var api = this.api();
                var start = api.page.info().start;
                api.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = start + i + 1;
                });
            }
        });

        $('#filter-session-status, #filter-session-creator').on('select2:select select2:unselect', function() {
            sessionTable.draw();
        });
    });
</script>
<?= $this->endSection() ?>