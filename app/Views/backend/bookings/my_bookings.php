<?php

/**
 * My Bookings — current user's own bookings with cancel action.
 */
?>

<?= $this->extend('default') ?>

<?= $this->section('content') ?>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">My Bookings</h3>
                    <div class="block-options">
                        <?php if (auth()->user()->can('booking.create')): ?>
                            <a href="<?= base_url('bookings/create') ?>" class="btn btn-sm btn-primary">
                                New Booking
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter w-100 nowrap" id="table-my-bookings">
                            <thead>
                                <tr>
                                    <th class="rowIndex text-center noOrder">#</th>
                                    <th class="bookingDate">Date</th>
                                    <th class="resourceName">Resource</th>
                                    <th class="timeSlot">Time</th>
                                    <th class="purposeName">Purpose</th>
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
                                            <?php if (in_array($b['status'], ['pending', 'approved'])): ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-cancel-booking"
                                                    data-id="<?= $b['id'] ?>"
                                                    data-bs-toggle="tooltip" title="Cancel Booking">
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

        if ($('#table-my-bookings').length) {
            $('#table-my-bookings').DataTable({
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
                        width: '10%'
                    },
                    {
                        targets: 'resourceName',
                        width: '15%'
                    },
                    {
                        targets: 'timeSlot',
                        width: '10%'
                    },
                    {
                        targets: 'purposeName',
                        width: '12%'
                    },
                    {
                        targets: 'remarksCol',
                        width: '16%'
                    },
                    {
                        targets: 'approvalRemarksCol',
                        width: '16%'
                    },
                    {
                        targets: 'bookingStatus',
                        width: '9%',
                        className: 'text-center'
                    },
                    {
                        targets: 'tableAction',
                        width: '9%',
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

        $(document).on('click', '.btn-cancel-booking', function() {
            var id = $(this).data('id');
            toast.fire({
                title: 'Cancel this booking?',
                html: 'The booking will be marked as cancelled.',
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