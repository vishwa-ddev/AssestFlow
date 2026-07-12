<?php
/**
 * AssetFlow - Resource Booking Controller
 */

class BookingController
{
    private Booking $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
    }

    public function index(): void
    {
        requireAuth();

        $action = $_GET['action'] ?? 'list';

        if ($action === 'book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->bookSlot();
            return;
        }

        $resources  = $this->bookingModel->getResources();
        $resourceId = (int) ($_GET['resource_id'] ?? ($resources[0]['id'] ?? 0));
        $date       = $_GET['date'] ?? date('Y-m-d');
        $bookings   = $resourceId ? $this->bookingModel->getBookingsForDate($resourceId, $date) : [];

        $conflict = false;
        if (!empty($_GET['check_start']) && !empty($_GET['check_end'])) {
            $conflict = $this->bookingModel->hasConflict(
                $resourceId,
                $date,
                $_GET['check_start'],
                $_GET['check_end']
            );
        }

        render('resource-booking/index', [
            'pageTitle'   => 'Resource Booking',
            'currentPage' => 'resource-booking',
            'resources'   => $resources,
            'resourceId'  => $resourceId,
            'date'        => $date,
            'bookings'    => $bookings,
            'conflict'    => $conflict,
            'flash'       => getFlash(),
            'user'        => currentUser(),
        ]);
    }

    private function bookSlot(): void
    {
        $resourceId = (int) ($_POST['resource_id'] ?? 0);
        $date         = $_POST['booking_date'] ?? date('Y-m-d');
        $startTime    = $_POST['start_time'] ?? '';
        $endTime      = $_POST['end_time'] ?? '';
        $resourceName = trim($_POST['resource_name'] ?? '');

        if (!$resourceId || $startTime === '' || $endTime === '') {
            setFlash('error', 'Please select a valid time slot.');
            redirect('resource-booking', ['resource_id' => $resourceId, 'date' => $date]);
        }

        if ($this->bookingModel->hasConflict($resourceId, $date, $startTime, $endTime)) {
            setFlash('error', 'Requested slot overlaps with existing booking.');
            redirect('resource-booking', [
                'resource_id' => $resourceId,
                'date'        => $date,
                'check_start' => $startTime,
                'check_end'   => $endTime,
            ]);
        }

        $user = currentUser();

        if ($this->bookingModel->createBooking([
            'resource_id'   => $resourceId,
            'resource_name' => $resourceName,
            'booked_by'     => $user['name'],
            'booking_date'  => $date,
            'start_time'    => $startTime,
            'end_time'      => $endTime,
        ])) {
            logActivity('booking', "{$resourceName} booking confirmed", 'bi-door-open', 'Booking');
            setFlash('success', 'Slot booked successfully.');
        } else {
            setFlash('error', 'Failed to book slot.');
        }

        redirect('resource-booking', ['resource_id' => $resourceId, 'date' => $date]);
    }
}
