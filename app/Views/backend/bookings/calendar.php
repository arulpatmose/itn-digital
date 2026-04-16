<?php

/**
 * Booking Calendar — read-only FullCalendar view with resource/purpose/status filters.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Booking Calendar</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('booking.create')): ?>
                            <a href="<?= base_url('bookings/create') ?>" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus me-1"></i> New Booking
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">

                    <!-- Filters -->
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-resource">
                                    Resource
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearCalendarFilter('filter-resource')">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-resource" style="width:100%;" data-placeholder="All Resources">
                                    <option></option>
                                    <?php foreach ($resources as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= esc($r['name']) ?> (<?= esc($r['type_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-purpose">
                                    Purpose
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearCalendarFilter('filter-purpose')">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-purpose" style="width:100%;" data-placeholder="All Purposes">
                                    <option></option>
                                    <?php foreach ($purposes as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center" for="filter-status">
                                    Status
                                    <button type="button" class="btn bg-transparent border-0 btn-alt-secondary btn-sm" onclick="clearCalendarFilter('filter-status')">Reset</button>
                                </label>
                                <select class="js-select2 form-control" id="filter-status" style="width:100%;" data-placeholder="All Statuses">
                                    <option></option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="d-flex flex-wrap gap-3 mb-3 small">
                        <span><i class="fa fa-circle me-1" style="color:#198754"></i> Approved</span>
                        <span><i class="fa fa-circle me-1" style="color:#e08500"></i> Pending</span>
                        <span><i class="fa fa-circle me-1" style="color:#dc3545"></i> Rejected</span>
                        <span><i class="fa fa-circle me-1" style="color:#6c757d"></i> Cancelled</span>
                    </div>

                    <!-- Calendar -->
                    <div id="booking-calendar"></div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="modal-event-detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modal-event-header">
                <h5 class="modal-title" id="modal-event-title">Booking Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th class="text-muted" style="width:38%">Resource</th>
                            <td id="ev-resource"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Type</th>
                            <td id="ev-type"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Date</th>
                            <td id="ev-date"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Time</th>
                            <td id="ev-time"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Purpose</th>
                            <td id="ev-purpose"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Requested By</th>
                            <td id="ev-user"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status</th>
                            <td id="ev-status"></td>
                        </tr>
                        <tr id="ev-remarks-row">
                            <th class="text-muted">Remarks</th>
                            <td id="ev-remarks"></td>
                        </tr>
                        <tr id="ev-approval-row">
                            <th class="text-muted">Approval Note</th>
                            <td id="ev-approval"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>

<script>
    var bookingCalendar;

    $(function() {

        var calendarEl = document.getElementById('booking-calendar');

        bookingCalendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth,multiMonthYear'
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day',
                list: 'List',
                multiMonthYear: 'Year'
            },
            height: 'auto',
            nowIndicator: true,
            editable: false,
            selectable: false,

            events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '<?= base_url('bookings/calendar-events') ?>',
                    type: 'GET',
                    data: {
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                        resource_id: $('#filter-resource').val(),
                        purpose_id: $('#filter-purpose').val(),
                        status: $('#filter-status').val()
                    },
                    dataType: 'json',
                    success: function(events) {
                        successCallback(events);
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            },

            eventClick: function(info) {
                var p = info.event.extendedProps;
                var statusColors = {
                    approved: '#198754',
                    pending: '#e08500',
                    rejected: '#dc3545',
                    cancelled: '#6c757d'
                };
                var color = statusColors[p.status] || '#0d6efd';

                var startDate = info.event.start;
                var dateStr = startDate ? startDate.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) : '—';

                var badgeHtml = '<span class="badge text-white" style="background:' + color + '">' +
                    p.status.charAt(0).toUpperCase() + p.status.slice(1) + '</span>';

                $('#modal-event-header').css('border-left', '4px solid ' + color);
                $('#modal-event-title').text('Booking #' + info.event.id);
                $('#ev-resource').text(p.resource || '—');
                $('#ev-type').text(p.resource_type || '—');
                $('#ev-date').text(dateStr);
                $('#ev-time').text(p.time_range || '—');
                $('#ev-purpose').text(p.purpose || '—');
                $('#ev-user').text(p.user || '—');
                $('#ev-status').html(badgeHtml);

                if (p.remarks) {
                    $('#ev-remarks').text(p.remarks);
                    $('#ev-remarks-row').show();
                } else {
                    $('#ev-remarks-row').hide();
                }

                if (p.approval_remarks) {
                    $('#ev-approval').text(p.approval_remarks);
                    $('#ev-approval-row').show();
                } else {
                    $('#ev-approval-row').hide();
                }

                $('#modal-event-detail').modal('show');
            },

            eventDidMount: function(info) {
                info.el.setAttribute('title', info.event.extendedProps.time_range + ' · ' + info.event.extendedProps.user);
                bsTooltip();
            }
        });

        bookingCalendar.render();

        // Initialise Select2 on filters
        ['#filter-resource', '#filter-purpose', '#filter-status'].forEach(function(sel) {
            $(sel).select2({
                placeholder: $(sel).data('placeholder') || 'All',
                dropdownParent: document.querySelector('#page-container')
            });
        });

        // Refetch on select or clear via Select2
        $('#filter-resource, #filter-purpose, #filter-status').on('select2:select select2:unselect', function() {
            bookingCalendar.refetchEvents();
        });

    });

    function clearCalendarFilter(id) {
        $('#' + id).val(null).trigger('change');
        bookingCalendar.refetchEvents();
    }
</script>
<?= $this->endSection() ?>