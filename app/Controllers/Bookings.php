<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\ResourceModel;
use App\Models\TimeSlotModel;
use App\Models\BookingPurposeModel;

class Bookings extends BaseController
{
    protected $bookingModel;
    protected $resourceModel;
    protected $timeSlotModel;
    protected $purposeModel;

    public function __construct()
    {
        $this->bookingModel  = new BookingModel();
        $this->resourceModel = new ResourceModel();
        $this->timeSlotModel = new TimeSlotModel();
        $this->purposeModel  = new BookingPurposeModel();
    }

    /**
     * Admin view — all bookings, with approve/reject actions.
     */
    public function index()
    {
        if (!auth()->user()->can('booking.approve')) {
            return redirect()->back()->with('error', 'You do not have permission to access that page!');
        }

        $bookings = $this->bookingModel->getFullDetails();

        $data = [
            'page_title'       => 'All Bookings',
            'page_description' => 'Manage and review resource booking requests.',
            'bookings'         => $bookings,
        ];

        return view('backend/bookings/index', $data);
    }

    /**
     * Current user's own bookings.
     */
    public function myBookings()
    {
        if (!auth()->user()->can('booking.access')) {
            return redirect()->back()->with('error', 'You do not have permission to access that page!');
        }

        $userId   = auth()->id();
        $bookings = $this->bookingModel->getFullDetails(['bookings.user_id' => $userId]);

        $data = [
            'page_title'       => 'My Bookings',
            'page_description' => 'View and manage your resource booking requests.',
            'bookings'         => $bookings,
        ];

        return view('backend/bookings/my_bookings', $data);
    }

    /**
     * Show the booking creation form.
     */
    public function create()
    {
        if (!auth()->user()->can('booking.create')) {
            return redirect()->back()->with('error', 'You do not have permission to create bookings!');
        }

        $data = [
            'page_title'       => 'New Booking',
            'page_description' => 'Submit a new resource booking request.',
            'resources'        => $this->resourceModel
                ->select('resources.*, resource_types.name as type_name')
                ->join('resource_types', 'resource_types.id = resources.type_id')
                ->where('resources.status', 1)
                ->orderBy('resources.name', 'ASC')
                ->findAll(),
            'time_slots'       => $this->timeSlotModel->orderBy('start_time')->findAll(),
            'purpose_groups'   => $this->getGroupedPurposes(),
        ];

        return view('backend/bookings/create', $data);
    }

    /**
     * Store a new booking.
     */
    public function store()
    {
        if (!auth()->user()->can('booking.create')) {
            return redirect()->back()->with('error', 'You do not have permission to create bookings!');
        }

        $resourceId = (int) $this->request->getPost('resource_id');
        $timeSlotId = (int) $this->request->getPost('time_slot_id');
        $date       = $this->request->getPost('booking_date');
        $purposeId  = (int) $this->request->getPost('purpose_id');
        $remarks    = trim($this->request->getPost('remarks') ?? '');

        if (!$resourceId || !$timeSlotId || !$date || !$purposeId) {
            return redirect()->back()->withInput()->with('error', 'Resource, date, time slot, and purpose are required.');
        }

        $resource = $this->resourceModel->find($resourceId);
        if (!$resource || (int) $resource['status'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'The selected resource is not available for booking.');
        }

        $purpose = $this->purposeModel->find($purposeId);
        if (!$purpose || (int) $purpose['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'The selected booking purpose is invalid.');
        }

        // Availability check
        if ($this->bookingModel->isSlotTaken($resourceId, $date, $timeSlotId)) {
            return redirect()->back()->withInput()->with('error', 'That time slot is already booked for the selected date. Please choose another.');
        }

        $data = [
            'user_id'      => auth()->id(),
            'resource_id'  => $resourceId,
            'purpose_id'   => $purposeId,
            'time_slot_id' => $timeSlotId,
            'booking_date' => $date,
            'status'       => 'pending',
            'remarks'      => $remarks ?: null,
        ];

        if ($this->bookingModel->insert($data, false)) {
            $bookingId = $this->bookingModel->getInsertID();
            log_activity('booking.created', 'booking', $bookingId, "Created booking for date {$date}");

            return redirect()->to('/bookings/my-bookings')->with('success', 'Your booking request has been submitted successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'There was an error submitting your booking. Please try again.');
    }

    /**
     * Approve a booking (AJAX).
     */
    public function approve()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking.approve')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id      = (int) $this->request->getPost('id');
        $remarks = trim($this->request->getPost('approval_remarks') ?? '');
        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Booking not found.']);
        }

        if ($booking['status'] !== 'pending') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Only pending bookings can be approved.']);
        }

        if ($this->bookingModel->isSlotTaken((int) $booking['resource_id'], $booking['booking_date'], (int) $booking['time_slot_id'], $id)) {
            return $this->response->setStatusCode(409)->setJSON(['status' => 'error', 'message' => 'This slot is no longer available for approval.']);
        }

        $updated = $this->bookingModel->update($id, [
            'status'           => 'approved',
            'approved_by'      => auth()->id(),
            'approval_remarks' => $remarks ?: null,
        ]);

        if ($updated) {
            log_activity('booking.approved', 'booking', $id, "Approved booking #{$id}");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Booking approved successfully.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not approve booking.']);
    }

    /**
     * Reject a booking (AJAX).
     */
    public function reject()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking.approve')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id      = (int) $this->request->getPost('id');
        $remarks = trim($this->request->getPost('approval_remarks') ?? '');
        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Booking not found.']);
        }

        if ($booking['status'] !== 'pending') {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Only pending bookings can be rejected.']);
        }

        $updated = $this->bookingModel->update($id, [
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'approval_remarks' => $remarks ?: null,
        ]);

        if ($updated) {
            log_activity('booking.rejected', 'booking', $id, "Rejected booking #{$id}");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Booking rejected.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not reject booking.']);
    }

    /**
     * Cancel a booking (AJAX).
     */
    public function cancel()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        if (!auth()->user()->can('booking.cancel')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Permission denied.']);
        }

        $id      = (int) $this->request->getPost('id');
        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Booking not found.']);
        }

        if (!in_array($booking['status'], ['pending', 'approved'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'Only pending or approved bookings can be cancelled.']);
        }

        // Only owner can cancel their own booking unless they have approve permission
        if ($booking['user_id'] !== auth()->id() && !auth()->user()->can('booking.approve')) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'You can only cancel your own bookings.']);
        }

        if ($this->bookingModel->update($id, ['status' => 'cancelled'])) {
            log_activity('booking.cancelled', 'booking', $id, "Cancelled booking #{$id}");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Booking cancelled successfully.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Could not cancel booking.']);
    }

    /**
     * AJAX — return available/unavailable time slots for a resource + date combo.
     */
    public function availableSlots()
    {
        if (!$this->request->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
        }

        $resourceId = (int) $this->request->getPost('resource_id');
        $date       = $this->request->getPost('booking_date');

        if (!$resourceId || !$date) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 'error', 'message' => 'resource_id and booking_date are required.']);
        }

        $allSlots = $this->timeSlotModel->orderBy('start_time')->findAll();

        // Get taken slot IDs for this resource+date
        $takenSlotIds = $this->bookingModel
            ->select('time_slot_id')
            ->where('resource_id', $resourceId)
            ->where('booking_date', $date)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->findAll();

        $takenIds = array_column($takenSlotIds, 'time_slot_id');

        $slots = [];
        foreach ($allSlots as $slot) {
            $slots[] = [
                'id'         => $slot['id'],
                'label'      => $slot['label'],
                'start_time' => $slot['start_time'],
                'end_time'   => $slot['end_time'],
                'available'  => !in_array($slot['id'], $takenIds),
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'slots' => $slots]);
    }

    protected function getGroupedPurposes(): array
    {
        return $this->purposeModel->getGroupedActivePurposes();
    }
}
