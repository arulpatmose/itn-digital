<?php

/**
 * Create Booking — interactive time grid, direct start_time / end_time submission.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-10 col-xl-7">
            <form action="<?= site_url('bookings/submit') ?>" method="POST" id="form-create-booking">
                <?= csrf_field() ?>
                <input type="hidden" id="start_time" name="start_time" value="<?= old('start_time') ?>">
                <input type="hidden" id="end_time" name="end_time" value="<?= old('end_time') ?>">

                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">New Booking Request</h3>
                        <div class="block-options">
                            <a href="<?= base_url('bookings/my-bookings') ?>" class="btn btn-sm btn-danger">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">Submit Request</button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row">
                            <div class="col-12">

                                <!-- Resource -->
                                <div class="mb-4">
                                    <label class="form-label" for="resource_id">Resource <span class="text-danger">*</span></label>
                                    <select class="form-select" id="resource_id" name="resource_id" required>
                                        <option value="">— Select a resource —</option>
                                        <?php foreach ($resources as $r): ?>
                                            <option value="<?= $r['id'] ?>" <?= old('resource_id') == $r['id'] ? 'selected' : '' ?>>
                                                <?= esc($r['name']) ?> (<?= esc($r['type_name'] ?? '') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Booking Date -->
                                <div class="mb-4">
                                    <label class="form-label" for="booking_date">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="booking_date" name="booking_date"
                                        min="<?= date('Y-m-d') ?>"
                                        value="<?= old('booking_date') ?>" required>
                                </div>

                                <!-- Time Range -->
                                <div class="mb-4">
                                    <label class="form-label">Time <span class="text-danger">*</span></label>

                                    <div id="slot-placeholder" class="text-muted small py-2">
                                        <i class="fa fa-info-circle me-1"></i> Select a resource and date to see available times.
                                    </div>
                                    <div id="slot-loading" class="d-none text-muted small py-2">
                                        <i class="fa fa-spinner fa-spin me-1"></i> Checking availability…
                                    </div>

                                    <div id="slot-controls" class="d-none">
                                        <div class="small text-muted mb-2">
                                            <i class="fa fa-circle text-success me-1"></i> Available &nbsp;
                                            <i class="fa fa-circle text-danger me-1"></i> Taken &nbsp;
                                            <i class="fa fa-circle text-primary me-1"></i> Your selection
                                        </div>
                                        <div id="slot-grid" class="d-flex flex-wrap gap-1 mb-2"></div>
                                        <div id="slot-grid-hint" class="small text-muted mt-1"></div>
                                    </div>
                                </div>

                                <!-- Purpose -->
                                <div class="mb-4">
                                    <label class="form-label" for="purpose_id">Purpose <span class="text-danger">*</span></label>
                                    <select class="form-select" id="purpose_id" name="purpose_id" required>
                                        <option value="">— Select purpose —</option>
                                        <?php foreach ($purpose_groups as $groupName => $groupPurposes): ?>
                                            <optgroup label="<?= esc($groupName) ?>">
                                                <?php foreach ($groupPurposes as $p): ?>
                                                    <option value="<?= $p['id'] ?>" <?= old('purpose_id') == $p['id'] ? 'selected' : '' ?>>
                                                        <?= esc($p['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Remarks -->
                                <div class="mb-4">
                                    <label class="form-label" for="remarks">Remarks <small class="text-muted">(optional)</small></label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3"
                                        placeholder="Any additional notes for the approver…"><?= old('remarks') ?></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('other-scripts') ?>
<script>
    $(function() {

        <?php if ($flash = session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#dc3545'
            });
        <?php endif; ?>

        var DAY_START = '05:00';
        var DAY_END = '24:00';
        var STEP_MINS = 30;

        var allSlotsData = [];
        var selStart = null; // "HH:MM" — booking start_time
        var selEnd = null; // "HH:MM" — booking end_time

        var oldStartTime = <?= json_encode(old('start_time') ?: '') ?>;
        var oldEndTime = <?= json_encode(old('end_time')   ?: '') ?>;

        // ── Helpers ──────────────────────────────────────────────────────────────

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function timeToMins(t) {
            var p = t.split(':');
            return parseInt(p[0], 10) * 60 + parseInt(p[1], 10);
        }

        function calcDuration(s, e) {
            var sm = timeToMins(s),
                em = timeToMins(e);
            if (em === 0) em = 24 * 60;
            if (em <= sm) em += 24 * 60;
            var mins = em - sm,
                hrs = Math.floor(mins / 60),
                rem = mins % 60,
                out = '';
            if (hrs) out += hrs + ' hr' + (hrs > 1 ? 's' : '');
            if (rem) out += (out ? ' ' : '') + rem + ' min';
            return out;
        }

        // ── Generate grid from booked ranges ─────────────────────────────────────

        function generateTimeGrid(bookedRanges) {
            var slots = [],
                start = timeToMins(DAY_START),
                end = timeToMins(DAY_END);
            for (var t = start; t < end; t += STEP_MINS) {
                var h1 = Math.floor(t / 60),
                    m1 = t % 60;
                var t2 = t + STEP_MINS,
                    h2 = Math.floor(t2 / 60) % 24,
                    m2 = t2 % 60;
                var ss = pad(h1) + ':' + pad(m1),
                    se = pad(h2) + ':' + pad(m2);
                var blocked = bookedRanges.some(function(b) {
                    var bs = b.start_time.substring(0, 5),
                        be = b.end_time.substring(0, 5);
                    return bs <= ss && be > ss;
                });
                slots.push({
                    start: ss,
                    end: se,
                    blocked: blocked
                });
            }
            return slots;
        }

        // ── Range clear check ─────────────────────────────────────────────────────

        function rangeClear(startTime, endSlotStart) {
            var si = allSlotsData.findIndex(function(s) {
                return s.start === startTime;
            });
            var ei = allSlotsData.findIndex(function(s) {
                return s.start === endSlotStart;
            });
            if (si === -1 || ei === -1 || ei < si) return false;
            for (var i = si; i <= ei; i++) {
                if (allSlotsData[i].blocked) return false;
            }
            return true;
        }

        // ── Hint + hidden inputs ──────────────────────────────────────────────────

        function refreshHint() {
            var $h = $('#slot-grid-hint');
            if (!selStart) {
                $h.html('<i class="fa fa-hand-pointer-o me-1"></i> Click a slot to set your start time.');
            } else if (!selEnd) {
                $h.html('<i class="fa fa-arrow-right me-1 text-primary"></i> Start: <strong>' + selStart + '</strong> — now click an end slot.');
            } else {
                $h.html(
                    '<i class="fa fa-check-circle me-1 text-success"></i>' +
                    ' <strong>' + selStart + '</strong> → <strong>' + selEnd + '</strong>' +
                    ' <span class="text-muted ms-1">(' + calcDuration(selStart, selEnd) + ')</span>' +
                    ' &nbsp;<a href="#" id="clear-selection" class="text-danger small ms-2"><i class="fa fa-times me-1"></i>Clear</a>'
                );
            }
            $('#start_time').val(selStart || '');
            $('#end_time').val(selEnd || '');
        }

        // ── Render grid ───────────────────────────────────────────────────────────

        function renderGrid() {
            var si = selStart ? allSlotsData.findIndex(function(s) {
                return s.start === selStart;
            }) : -1;
            var ei = selEnd ? allSlotsData.findIndex(function(s) {
                return s.end === selEnd;
            }) : -1;
            var $grid = $('#slot-grid').empty();

            $.each(allSlotsData, function(i, slot) {
                var inRange = si >= 0 && ei >= 0 && i >= si && i <= ei;
                var cls = inRange ? 'btn-primary' : (slot.blocked ? 'btn-outline-danger' : 'btn-outline-success');

                $('<button type="button">')
                    .addClass('btn btn-xs ' + cls)
                    .attr('data-slot-start', slot.start)
                    .attr('title', slot.start + (slot.blocked ? ' — taken' : ''))
                    .css({
                        fontSize: '0.7rem',
                        padding: '2px 5px',
                        minWidth: '48px'
                    })
                    .text(slot.start)
                    .prop('disabled', slot.blocked)
                    .appendTo($grid);
            });

            refreshHint();
        }

        // ── Grid click ────────────────────────────────────────────────────────────

        $(document).on('click', '#slot-grid button[data-slot-start]', function() {
            var clickedStart = $(this).data('slot-start');
            var slot = allSlotsData.find(function(s) {
                return s.start === clickedStart;
            });
            if (!slot || slot.blocked) return;

            if (!selStart) {
                selStart = clickedStart;
                selEnd = null;
            } else if (clickedStart === selStart) {
                selStart = null;
                selEnd = null;
            } else if (selEnd !== null) {
                selStart = clickedStart;
                selEnd = null;
            } else {
                var ci = allSlotsData.findIndex(function(s) {
                    return s.start === clickedStart;
                });
                var si = allSlotsData.findIndex(function(s) {
                    return s.start === selStart;
                });
                if (ci < si) {
                    selStart = clickedStart;
                } else if (rangeClear(selStart, clickedStart)) {
                    selEnd = slot.end;
                } else {
                    selStart = clickedStart;
                    selEnd = null;
                }
            }

            renderGrid();
        });

        $(document).on('click', '#clear-selection', function(e) {
            e.preventDefault();
            selStart = null;
            selEnd = null;
            renderGrid();
        });

        // ── Load availability ─────────────────────────────────────────────────────

        function loadSlots() {
            var resourceId = $('#resource_id').val();
            var bookingDate = $('#booking_date').val();

            if (!resourceId || !bookingDate) {
                $('#slot-controls').addClass('d-none');
                $('#slot-loading').addClass('d-none');
                $('#slot-placeholder').removeClass('d-none');
                return;
            }

            selStart = null;
            selEnd = null;

            $('#slot-placeholder').addClass('d-none');
            $('#slot-controls').addClass('d-none');
            $('#slot-loading').removeClass('d-none');

            $.ajax({
                url: _AppUri + '/bookings/available-slots',
                type: 'POST',
                data: {
                    resource_id: resourceId,
                    booking_date: bookingDate
                },
                dataType: 'json',
                success: function(res) {
                    $('#slot-loading').addClass('d-none');
                    if (res.status !== 'success') {
                        $('#slot-placeholder').text('Could not load availability.').removeClass('d-none');
                        return;
                    }
                    allSlotsData = generateTimeGrid(res.bookings || []);

                    if (oldStartTime) {
                        selStart = oldStartTime;
                        oldStartTime = '';
                    }
                    if (oldEndTime) {
                        selEnd = oldEndTime;
                        oldEndTime = '';
                    }

                    renderGrid();
                    $('#slot-controls').removeClass('d-none');
                },
                error: function() {
                    $('#slot-loading').addClass('d-none');
                    $('#slot-placeholder').text('Error loading availability.').removeClass('d-none');
                }
            });
        }

        $('#resource_id, #booking_date').on('change', loadSlots);

        // ── Submit validation ─────────────────────────────────────────────────────

        $('#form-create-booking').on('submit', function(e) {
            if (!selStart || !selEnd) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Select a time range',
                    text: 'Please click a start and end slot on the grid before submitting.'
                });
            }
        });

        if ($('#resource_id').val() && $('#booking_date').val()) {
            loadSlots();
        }

    });
</script>
<?= $this->endSection() ?>