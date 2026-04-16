<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\ResourceModel;
use App\Models\BookingPurposeModel;

class Bookings extends BaseController
{
    protected $bookingModel;
    protected $resourceModel;
    protected $purposeModel;

    public function __construct()
    {
        $this->bookingModel  = new BookingModel();
        $this->resourceModel = new ResourceModel();
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

        $data = [
            'page_title'       => 'All Bookings',
            'page_description' => 'Manage and review resource booking requests.',
            'bookings'         => $this->bookingModel->getFullDetails(),
            'resources'        => $this->resourceModel
                ->select('resources.id, resources.name, resource_types.name as type_name')
                ->join('resource_types', 'resource_types.id = resources.type_id')
                ->orderBy('resources.name', 'ASC')
                ->findAll(),
            'purposes'         => $this->purposeModel->orderBy('name', 'ASC')->findAll(),
            'users'            => $this->bookingModel->getBookingUsersList(),
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

        $data = [
            'page_title'       => 'My Bookings',
            'page_description' => 'View and manage your resource booking requests.',
            'bookings'         => $this->bookingModel->getFullDetails(['bookings.user_id' => auth()->id()]),
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
        $date       = $this->request->getPost('booking_date');
        $startTime  = trim($this->request->getPost('start_time') ?? '');
        $endTime    = trim($this->request->getPost('end_time')   ?? '');
        $purposeId  = (int) $this->request->getPost('purpose_id');
        $remarks    = trim($this->request->getPost('remarks') ?? '');

        if (!$resourceId || !$date || !$startTime || !$endTime || !$purposeId) {
            return redirect()->back()->withInput()->with('error', 'Resource, date, start time, end time, and purpose are required.');
        }

        // Validate time format (HH:MM or HH:MM:SS)
        $timePattern = '/^\d{2}:\d{2}(:\d{2})?$/';
        if (!preg_match($timePattern, $startTime) || !preg_match($timePattern, $endTime)) {
            return redirect()->back()->withInput()->with('error', 'The time format is invalid.');
        }

        if ($startTime >= $endTime) {
            return redirect()->back()->withInput()->with('error', 'End time must be after start time.');
        }

        // Validate date
        $parsedDate = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
            return redirect()->back()->withInput()->with('error', 'The booking date is invalid.');
        }
        if ($date < date('Y-m-d')) {
            return redirect()->back()->withInput()->with('error', 'The booking date cannot be in the past.');
        }

        $resource = $this->resourceModel->find($resourceId);
        if (!$resource || (int) $resource['status'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'The selected resource is not available for booking.');
        }

        $purpose = $this->purposeModel->find($purposeId);
        if (!$purpose || (int) $purpose['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'The selected booking purpose is invalid.');
        }

        if ($this->bookingModel->isSlotTaken($resourceId, $date, $startTime, $endTime)) {
            return redirect()->back()->withInput()->with('error', 'The selected time range overlaps with an existing booking. Please choose another time.');
        }

        $data = [
            'user_id'      => auth()->id(),
            'resource_id'  => $resourceId,
            'purpose_id'   => $purposeId,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
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

        if ($this->bookingModel->isSlotTaken(
            (int) $booking['resource_id'],
            $booking['booking_date'],
            $booking['start_time'],
            $booking['end_time'],
            $id
        )) {
            return $this->response->setStatusCode(409)->setJSON(['status' => 'error', 'message' => 'This time range is no longer available — another booking was approved first.']);
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
     * AJAX — return existing booked ranges for a resource + date.
     * The client generates the time grid and marks blocked slots itself.
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

        $bookings = $this->bookingModel->db->table('bookings')
            ->select('start_time, end_time')
            ->where('resource_id', $resourceId)
            ->where('booking_date', $date)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['status' => 'success', 'bookings' => $bookings]);
    }

    /**
     * Calendar view — read-only visual calendar of all bookings.
     */
    public function calendar()
    {
        if (!auth()->user()->can('booking.access')) {
            return redirect()->back()->with('error', 'You do not have permission to access that page!');
        }

        $resources = $this->resourceModel
            ->select('resources.id, resources.name, resource_types.name as type_name')
            ->join('resource_types', 'resource_types.id = resources.type_id')
            ->where('resources.status', 1)
            ->orderBy('resources.name', 'ASC')
            ->findAll();

        $purposes = $this->purposeModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'page_title'       => 'Booking Calendar',
            'page_description' => 'Visual calendar of all resource bookings.',
            'resources'        => $resources,
            'purposes'         => $purposes,
        ];

        return view('backend/bookings/calendar', $data);
    }

    /**
     * AJAX — return bookings as FullCalendar events JSON.
     * Params: start, end (ISO date strings from FullCalendar), resource_id, purpose_id, status.
     */
    public function calendarEvents()
    {
        if (!auth()->user()->can('booking.access')) {
            return $this->response->setStatusCode(403)->setJSON([]);
        }

        $start      = $this->request->getGet('start');
        $end        = $this->request->getGet('end');
        $resourceId = (int) $this->request->getGet('resource_id');
        $purposeId  = (int) $this->request->getGet('purpose_id');
        $status     = $this->request->getGet('status');

        $bookings = $this->bookingModel->getCalendarEvents($start, $end, $resourceId, $purposeId, $status);

        $statusColors = [
            'approved'  => '#198754',
            'pending'   => '#e08500',
            'rejected'  => '#dc3545',
            'cancelled' => '#6c757d',
        ];

        $events = [];
        foreach ($bookings as $b) {
            $color = $statusColors[$b['status']] ?? '#0d6efd';
            $events[] = [
                'id'    => $b['id'],
                'title' => $b['resource_name'] . ' — ' . $b['purpose_name'],
                'start' => $b['booking_date'] . 'T' . $b['start_time'],
                'end'   => $b['booking_date'] . 'T' . $b['end_time'],
                'color' => $color,
                'extendedProps' => [
                    'status'           => $b['status'],
                    'resource'         => $b['resource_name'],
                    'resource_type'    => $b['resource_type'],
                    'purpose'          => $b['purpose_name'],
                    'user'             => $b['user_name'],
                    'remarks'          => $b['remarks'] ?? '',
                    'approval_remarks' => $b['approval_remarks'] ?? '',
                    'time_range'       => substr($b['start_time'], 0, 5) . ' – ' . substr($b['end_time'], 0, 5),
                ],
            ];
        }

        return $this->response->setJSON($events);
    }

    protected function getGroupedPurposes(): array
    {
        return $this->purposeModel->getGroupedActivePurposes();
    }
}
