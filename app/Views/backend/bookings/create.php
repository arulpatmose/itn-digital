<?php

/**
 * Create Booking — form with AJAX time slot availability check.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-md-12 col-lg-10 col-xl-7">
            <form action="<?= site_url('bookings/submit') ?>" method="POST" id="form-create-booking">
                <?= csrf_field() ?>
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
                                            <option value="<?= $r['id'] ?>"><?= esc($r['name']) ?> <span class="text-muted">(<?= esc($r['type_name'] ?? '') ?>)</span></option>
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

                                <!-- Time Slot -->
                                <div class="mb-4">
                                    <label class="form-label">Time Slot <span class="text-danger">*</span></label>
                                    <div id="slot-placeholder" class="text-muted small py-2">
                                        <i class="fa fa-info-circle me-1"></i> Select a resource and date to see available slots.
                                    </div>
                                    <div id="slot-loading" class="d-none text-muted small py-2">
                                        <i class="fa fa-spinner fa-spin me-1"></i> Checking availability…
                                    </div>
                                    <div id="slot-grid" class="d-none row g-2 mt-1"></div>
                                    <input type="hidden" id="time_slot_id" name="time_slot_id" required>
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

        // Flash error from redirect()->withInput()
        <?php if ($flash = session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode($flash) ?>,
                confirmButtonColor: '#dc3545'
            });
        <?php endif; ?>

        function loadSlots() {
            var resourceId = $('#resource_id').val();
            var bookingDate = $('#booking_date').val();

            if (!resourceId || !bookingDate) {
                $('#slot-grid').addClass('d-none');
                $('#slot-loading').addClass('d-none');
                $('#slot-placeholder').removeClass('d-none');
                $('#time_slot_id').val('');
                return;
            }

            $('#slot-placeholder').addClass('d-none');
            $('#slot-grid').addClass('d-none');
            $('#slot-loading').removeClass('d-none');
            $('#time_slot_id').val('');

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
                        $('#slot-placeholder').text('Could not load slots.').removeClass('d-none');
                        return;
                    }

                    var $grid = $('#slot-grid').empty().removeClass('d-none');

                    $.each(res.slots, function(i, slot) {
                        var available = slot.available;
                        var label = slot.label + '<small class="d-block text-muted">' + slot.start_time.substring(0, 5) + ' – ' + slot.end_time.substring(0, 5) + '</small>';

                        var $btn = $('<div class="col-6 col-md-4"></div>').append(
                            $('<button type="button"></button>')
                            .addClass('btn btn-sm w-100 slot-btn ' + (available ? 'btn-outline-primary' : 'btn-outline-secondary disabled'))
                            .attr('data-slot-id', slot.id)
                            .prop('disabled', !available)
                            .html(label + (!available ? '<span class="badge bg-danger ms-1">Taken</span>' : ''))
                        );

                        $grid.append($btn);
                    });
                },
                error: function() {
                    $('#slot-loading').addClass('d-none');
                    $('#slot-placeholder').text('Error loading slots.').removeClass('d-none');
                }
            });
        }

        $('#resource_id, #booking_date').on('change', loadSlots);

        $(document).on('click', '.slot-btn:not(.disabled)', function() {
            $('.slot-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
            $('#time_slot_id').val($(this).data('slot-id'));
        });

        // Validate slot selected before submit
        $('#form-create-booking').on('submit', function(e) {
            if (!$('#time_slot_id').val()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Select a time slot',
                    text: 'Please choose an available time slot before submitting.'
                });
            }
        });

    });
</script>
<?= $this->endSection() ?>
