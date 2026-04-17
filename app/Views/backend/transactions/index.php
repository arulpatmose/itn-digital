<?php

/** @var array $transactions */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Quick Actions</h3>
                </div>
                <div class="block-content">
                    <div class="row g-3 pb-3">
                        <?php if (auth()->user()->can('transactions.receive')): ?>
                            <div class="col-6 col-md-3">
                                <a href="<?= base_url('transactions/receive') ?>" class="btn btn-success w-100 py-3">
                                    <i class="fa fa-arrow-circle-down d-block fs-3 mb-1"></i>
                                    Receive
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (auth()->user()->can('transactions.transfer')): ?>
                            <div class="col-6 col-md-3">
                                <a href="<?= base_url('transactions/transfer') ?>" class="btn btn-info w-100 py-3">
                                    <i class="fa fa-exchange-alt d-block fs-3 mb-1"></i>
                                    Transfer
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (auth()->user()->can('transactions.handover')): ?>
                            <div class="col-6 col-md-3">
                                <a href="<?= base_url('transactions/handover') ?>" class="btn btn-warning w-100 py-3">
                                    <i class="fa fa-hand-holding d-block fs-3 mb-1"></i>
                                    Handover
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (auth()->user()->can('transactions.ingest')): ?>
                            <div class="col-6 col-md-3">
                                <a href="<?= base_url('transactions/ingest') ?>" class="btn btn-primary w-100 py-3">
                                    <i class="fa fa-layer-group d-block fs-3 mb-1"></i>
                                    Ingest
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Log -->
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Transaction Log</h3>
                </div>
                <div class="block-content block-content-full">

                    <!-- Filters -->
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-tx-type">
                                    Type
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearTxFilter('filter-tx-type')">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-tx-type" style="width:100%;" data-placeholder="All Types">
                                    <option></option>
                                    <option value="RECEIVE">Receive</option>
                                    <option value="TRANSFER">Transfer</option>
                                    <option value="HANDOVER">Handover</option>
                                    <option value="INGEST">Ingest</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-tx-handler">
                                    Handled By
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearTxFilter('filter-tx-handler')">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-tx-handler" style="width:100%;" data-placeholder="All Users">
                                    <option></option>
                                    <?php
                                    $handlers = array_unique(array_filter(array_column($transactions, 'handler_name')));
                                    sort($handlers);
                                    foreach ($handlers as $h): ?>
                                        <option value="<?= esc($h) ?>"><?= esc($h) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    Search
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="$('#filter-tx-search').val(''); txTable.search('').draw();">Reset</button>
                                </label>
                                <input type="text" class="form-control" id="filter-tx-search" placeholder="Search chip count, participant, session…">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-transactions">
                            <thead>
                                <tr>
                                    <th class="text-center col-tx-index">#</th>
                                    <th class="col-tx-date">Date</th>
                                    <th class="col-tx-type">Type</th>
                                    <th class="col-tx-chips">Chips</th>
                                    <th class="col-tx-from">From</th>
                                    <th class="col-tx-to">To</th>
                                    <th class="col-tx-session">Session</th>
                                    <th class="col-tx-handler">Handled By</th>
                                    <th class="col-tx-remarks">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $i => $tx): ?>
                                    <tr data-type="<?= esc($tx['transaction_type']) ?>" data-handler="<?= esc($tx['handler_name'] ?? '') ?>">
                                        <td class="text-muted text-center"><?= $i + 1 ?></td>
                                        <td class="text-nowrap"><?= date('d M Y H:i', strtotime($tx['created_at'])) ?></td>
                                        <td>
                                            <?php
                                            $txClass = match ($tx['transaction_type']) {
                                                'RECEIVE'  => 'bg-success',
                                                'TRANSFER' => 'bg-info',
                                                'HANDOVER' => 'bg-warning',
                                                'INGEST'   => 'bg-primary',
                                                'RETURN'   => 'bg-secondary',
                                                default    => 'bg-dark',
                                            };
                                            ?>
                                            <span class="badge <?= $txClass ?>"><?= esc($tx['transaction_type']) ?></span>
                                        </td>
                                        <td><?= (int) $tx['chip_count'] ?></td>
                                        <td><?= $tx['from_name'] ? esc($tx['from_name']) : '<span class="text-muted">—</span>' ?></td>
                                        <td><?php
                                            echo match($tx['to_location'] ?? null) {
                                                'digital_unit' => '<i class="fa fa-building fa-fw text-muted"></i> ITN Digital',
                                                'library'  => '<i class="fa fa-book fa-fw text-muted"></i> Library',
                                                'ingest'   => '<span class="text-primary"><i class="fa fa-layer-group fa-fw"></i> Ingest</span>',
                                                'producer' => $tx['to_name'] ? esc($tx['to_name']) : '<span class="text-muted">—</span>',
                                                default    => '<span class="text-muted">—</span>',
                                            };
                                        ?></td>
                                        <td>
                                            <?php if ($tx['session_title'] && $tx['ingest_session_id']): ?>
                                                <a href="<?= base_url('ingest-sessions/' . $tx['ingest_session_id']) ?>">
                                                    <?= esc($tx['session_title']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($tx['handler_name'] ?? '—') ?></td>
                                        <td class="text-muted small"><?= esc($tx['remarks'] ?? '—') ?></td>
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

        ['#filter-tx-type', '#filter-tx-handler'].forEach(function(sel) {
            $(sel).select2({ placeholder: $(sel).data('placeholder') || 'All', dropdownParent: document.querySelector('#page-container') });
        });

        function clearTxFilter(id) {
            $('#' + id).val(null).trigger('change');
        }

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'table-transactions') return true;
            var row     = $(settings.aoData[dataIndex].nTr);
            var type    = $('#filter-tx-type').val();
            var handler = $('#filter-tx-handler').val();
            if (type    && row.data('type')    !== type)    return false;
            if (handler && row.data('handler') !== handler) return false;
            return true;
        });

        $('#filter-tx-search').on('input', function() {
            txTable.search(this.value).draw();
        });

        var txTable = $('#table-transactions').DataTable({
            dom: 'lrtip',
            pagingType: 'full_numbers',
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            autoWidth: true,
            scrollX: true,
            stateSave: true,
            order: [[1, 'desc']],
            columnDefs: [
                { targets: 'col-tx-index',   width: '3%',  orderable: false, className: 'text-center' },
                { targets: 'col-tx-date',    width: '10%' },
                { targets: 'col-tx-type',    width: '8%' },
                { targets: 'col-tx-chips',   width: '5%' },
                { targets: 'col-tx-from',    width: '12%' },
                { targets: 'col-tx-to',      width: '12%' },
                { targets: 'col-tx-session', width: '15%' },
                { targets: 'col-tx-handler', width: '12%' },
                { targets: 'col-tx-remarks', width: '23%' },
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

        $('#filter-tx-type, #filter-tx-handler').on('select2:select select2:unselect', function() {
            txTable.draw();
        });
    });
</script>
<?= $this->endSection() ?>