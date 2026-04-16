<?php

/**
 * All Bookings — admin view with approve / reject actions.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">All Bookings</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-bookings">
                            <thead>
                                <tr>
                                    <th class="rowIndex text-center noOrder">#</th>
                                    <th class="bookingDate">Date</th>
                                    <th class="resourceName">Resource</th>
                                    <th class="timeSlot">Time</th>
                                    <th class="purposeName">Purpose</th>
                                    <th class="requestedBy">Requested By</th>
                                    <th class="remarksCol">Remarks</th>
                                    <th class="approvalRemarksCol">Approval Note</th>
                                    <th class="bookingStatus text-center">Status</th>
                                    <th class="tableAction all text-center noOrder">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $i => $b): ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td><?= esc(date('d M Y', strtotime($b['booking_date']))) ?></td>
                                        <td>
                                            <?= esc($b['resource_name']) ?>
                                            <small class="text-muted d-block"><?= esc($b['resource_type']) ?></small>
                                        </td>
                                        <td><?= esc(substr($b['time_start'], 0, 5)) ?> – <?= esc(substr($b['time_end'], 0, 5)) ?></td>
                                        <td><?= esc($b['booking_purpose'] ?? '—') ?></td>
                                        <td><?= esc($b['user_name']) ?></td>
                                        <td class="text-muted small"><?= esc($b['remarks'] ?? '—') ?></td>
                                        <td class="text-muted small"><?= esc($b['approval_remarks'] ?? '—') ?></td>
                                        <td class="text-center">
                                            <?php
                                            $statusClass = match ($b['status']) {
                                                'approved'  => 'bg-success',
                                                'rejected'  => 'bg-danger',
                                                'cancelled' => 'bg-secondary',
                                                default     => 'bg-warning text-dark',
                                            };
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= ucfirst($b['status']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($b['status'] === 'pending' && auth()->user()->can('booking.approve')): ?>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-success btn-approve-booking"
                                                        data-id="<?= $b['id'] ?>" data-bs-toggle="tooltip" title="Approve">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger btn-reject-booking"
                                                        data-id="<?= $b['id'] ?>" data-bs-toggle="tooltip" title="Reject">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            <?php elseif ($b['status'] === 'approved' && auth()->user()->can('booking.approve')): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-cancel-booking"
                                                    data-id="<?= $b['id'] ?>" data-bs-toggle="tooltip" title="Cancel">
                                                    <i class="fa fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
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

<!-- Approve/Reject Modal -->
<div class="modal fade" id="modal-booking-action" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-booking-action-title">Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Remarks <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" id="approval-remarks" rows="3" placeholder="Add any notes or reasons..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-booking-action">Confirm</button>
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

        if ($('#table-bookings').length) {
            $('#table-bookings').DataTable({
                pagingType: 'full_numbers',
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 15, 20, 50],
                    [5, 10, 15, 20, 50]
                ],
                autoWidth: true,
                order: [
                    [1, 'desc']
                ],
                scrollX: true,
                stateSave: true,
                info: true,
                columnDefs: [{
                        targets: 'rowIndex',
                        width: '3%',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        targets: 'bookingDate',
                        width: '9%'
                    },
                    {
                        targets: 'resourceName',
                        width: '14%'
                    },
                    {
                        targets: 'timeSlot',
                        width: '9%'
                    },
                    {
                        targets: 'purposeName',
                        width: '10%'
                    },
                    {
                        targets: 'requestedBy',
                        width: '10%'
                    },
                    {
                        targets: 'remarksCol',
                        width: '13%'
                    },
                    {
                        targets: 'approvalRemarksCol',
                        width: '13%'
                    },
                    {
                        targets: 'bookingStatus',
                        width: '8%',
                        className: 'text-center'
                    },
                    {
                        targets: 'tableAction',
                        width: '11%',
                        className: 'text-center',
                        orderable: false
                    }
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
        }

        // Approve / Reject modal
        var _pendingAction = null;
        var _pendingId = null;

        $(document).on('click', '.btn-approve-booking', function() {
            _pendingAction = 'approve';
            _pendingId = $(this).data('id');
            $('#modal-booking-action-title').text('Approve Booking');
            $('#btn-confirm-booking-action').removeClass('btn-danger').addClass('btn-success').text('Approve');
            $('#approval-remarks').val('');
            $('#modal-booking-action').modal('show');
        });

        $(document).on('click', '.btn-reject-booking', function() {
            _pendingAction = 'reject';
            _pendingId = $(this).data('id');
            $('#modal-booking-action-title').text('Reject Booking');
            $('#btn-confirm-booking-action').removeClass('btn-success').addClass('btn-danger').text('Reject');
            $('#approval-remarks').val('');
            $('#modal-booking-action').modal('show');
        });

        $('#btn-confirm-booking-action').on('click', function() {
            $.ajax({
                url: _AppUri + '/bookings/' + _pendingAction,
                type: 'POST',
                data: {
                    id: _pendingId,
                    approval_remarks: $('#approval-remarks').val()
                },
                dataType: 'json',
                success: function(res) {
                    $('#modal-booking-action').modal('hide');
                    if (res.status === 'success') {
                        toast.fire({
                            icon: 'success',
                            title: res.message
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        toast.fire({
                            icon: 'error',
                            title: res.message
                        });
                    }
                },
                error: function() {
                    toast.fire({
                        icon: 'error',
                        title: 'An error occurred. Please try again.'
                    });
                }
            });
        });

        // Cancel booking
        $(document).on('click', '.btn-cancel-booking', function() {
            var id = $(this).data('id');
            toast.fire({
                title: 'Cancel this booking?',
                html: 'This action cannot be undone.',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it',
                confirmButtonColor: '#6c757d',
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: _AppUri + '/bookings/cancel',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                toast.fire({
                                    icon: 'success',
                                    title: res.message
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                toast.fire({
                                    icon: 'error',
                                    title: res.message
                                });
                            }
                        },
                        error: function() {
                            toast.fire({
                                icon: 'error',
                                title: 'An error occurred.'
                            });
                        }
                    });
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>