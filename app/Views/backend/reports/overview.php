<?php

/** @var array $chips, $participants */ ?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">

    <!-- Summary cards -->
    <div class="row">
        <?php
        $byType = array_count_values(array_column($chips, 'chip_type'));
        $unassigned = count(array_filter($chips, fn($c) => empty($c['holder_name'])));
        $total = count($chips);
        ?>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-primary"><?= $total ?></div>
                    <div class="text-muted">Total Chips</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-success"><?= $total - $unassigned ?></div>
                    <div class="text-muted">Assigned</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-warning"><?= $unassigned ?></div>
                    <div class="text-muted">Unassigned</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-info"><?= count($participants) ?></div>
                    <div class="text-muted">Participants</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chips table -->
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">All Chips — Current State</h3>
                </div>
                <div class="block-content block-content-full">

                    <!-- Filters -->
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-chip-type">
                                    Type
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm js-chip-filter-reset" data-target="filter-chip-type">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-chip-type" style="width:100%;" data-placeholder="All Types">
                                    <option></option>
                                    <option value="SXS">SXS</option>
                                    <option value="SD">SD</option>
                                    <option value="MicroSD">MicroSD</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-chip-location">
                                    Location
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm js-chip-filter-reset" data-target="filter-chip-location">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-chip-location" style="width:100%;" data-placeholder="All Locations">
                                    <option></option>
                                    <option value="digital_unit">ITN Digital</option>
                                    <option value="library">Library</option>
                                    <option value="producer">Producer</option>
                                    <option value="ingest">Ingest</option>
                                    <option value="unassigned">Unassigned</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    Search
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm js-chip-search-reset">Reset</button>
                                </label>
                                <input type="text" class="form-control" id="filter-chip-search" placeholder="Search chip code, holder…">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-overview">
                            <thead>
                                <tr>
                                    <th class="text-center col-chip-index">#</th>
                                    <th class="col-chip-code">Chip Code</th>
                                    <th class="col-chip-type">Type</th>
                                    <th class="col-chip-holder">Current Holder</th>
                                    <th class="col-chip-loc">Holder Type</th>
                                    <th class="text-center col-chip-actions">History</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chips as $i => $chip): ?>
                                    <tr data-type="<?= esc($chip['chip_type']) ?>" data-location="<?= esc($chip['to_location'] ?? 'unassigned') ?>">
                                        <td class="text-muted text-center"><?= $i + 1 ?></td>
                                        <td><strong><?= esc($chip['chip_code']) ?></strong></td>
                                        <td>
                                            <?php
                                            $typeClass = match ($chip['chip_type']) {
                                                'SXS'     => 'bg-primary',
                                                'SD'      => 'bg-info',
                                                'MicroSD' => 'bg-warning',
                                                'Other'   => 'bg-success',
                                                default   => 'bg-secondary',
                                            };
                                            ?>
                                            <span class="badge <?= $typeClass ?>"><?= esc($chip['chip_type']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($chip['to_location'] === 'digital_unit'): ?>
                                                <i class="fa fa-building fa-fw text-muted"></i> ITN Digital
                                            <?php elseif ($chip['to_location'] === 'library'): ?>
                                                <i class="fa fa-book fa-fw text-muted"></i> Library
                                            <?php elseif ($chip['to_location'] === 'producer' && $chip['holder_name']): ?>
                                                <?= esc($chip['holder_name']) ?>
                                            <?php elseif ($chip['to_location'] === 'ingest' && ($chip['ingest_session_title'] ?? '')): ?>
                                                <span class="text-warning"><i class="fa fa-download fa-fw"></i> At Ingest: <?= esc($chip['ingest_session_title']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $locBadge = match ($chip['to_location'] ?? null) {
                                                'digital_unit' => ['ITN Digital', 'bg-info'],
                                                'library'  => ['Library',     'bg-warning'],
                                                'producer' => ['Producer',    'bg-secondary'],
                                                'ingest'   => ['Ingest',      'bg-primary'],
                                                default    => null,
                                            };
                                            echo $locBadge
                                                ? '<span class="badge ' . $locBadge[1] . '">' . $locBadge[0] . '</span>'
                                                : '—';
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('reports/chip-history/' . $chip['id']) ?>"
                                                class="btn btn-sm btn-alt-secondary">
                                                <i class="fa fa-history"></i> Timeline
                                            </a>
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
        ['#filter-chip-type', '#filter-chip-location'].forEach(function(sel) {
            $(sel).select2({ placeholder: $(sel).data('placeholder') || 'All', dropdownParent: document.querySelector('#page-container') });
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'table-overview') return true;
            var row      = $(settings.aoData[dataIndex].nTr);
            var type     = $('#filter-chip-type').val();
            var location = $('#filter-chip-location').val();
            if (type     && row.data('type')     !== type)     return false;
            if (location && row.data('location') !== location) return false;
            return true;
        });

        var chipsTable = $('#table-overview').DataTable({
            dom: 'lrtip',
            pagingType: 'full_numbers',
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            autoWidth: true,
            scrollX: true,
            stateSave: true,
            order: [[1, 'asc']],
            columnDefs: [
                { targets: 'col-chip-index',   width: '4%',  orderable: false, className: 'text-center' },
                { targets: 'col-chip-code',    width: '15%' },
                { targets: 'col-chip-type',    width: '12%' },
                { targets: 'col-chip-holder',  width: '35%' },
                { targets: 'col-chip-loc',     width: '14%' },
                { targets: 'col-chip-actions', width: '20%', orderable: false, className: 'text-center' },
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

        $('#filter-chip-search').on('input', function() {
            chipsTable.search(this.value).draw();
        });

        $('#filter-chip-type, #filter-chip-location').on('select2:select select2:unselect change', function() {
            chipsTable.draw();
        });

        $('.js-chip-filter-reset').on('click', function() {
            $('#' + $(this).data('target')).val(null).trigger('change');
        });

        $('.js-chip-search-reset').on('click', function() {
            $('#filter-chip-search').val('');
            chipsTable.search('').draw();
        });
    });
</script>
<?= $this->endSection() ?>