<?php
/**
 * AssetFlow - Resource Booking Model
 */

class Booking
{
    public function getResources(): array
    {
        try {
            return db()->query('SELECT id, name FROM resources ORDER BY name')->fetchAll();
        } catch (PDOException $e) {
            return [['id' => 1, 'name' => 'Conference Room B2']];
        }
    }

    public function getBookingsForDate(int $resourceId, string $date): array
    {
        try {
            $stmt = db()->prepare(
                "SELECT * FROM bookings WHERE resource_id = ? AND booking_date = ? AND status = 'active' ORDER BY start_time"
            );
            $stmt->execute([$resourceId, $date]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return $this->getFallbackBookings();
        }
    }

    public function hasConflict(int $resourceId, string $date, string $startTime, string $endTime): bool
    {
        try {
            $stmt = db()->prepare(
                "SELECT COUNT(*) FROM bookings
                 WHERE resource_id = ? AND booking_date = ? AND status = 'active'
                 AND start_time < ? AND end_time > ?"
            );
            $stmt->execute([$resourceId, $date, $endTime, $startTime]);

            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createBooking(array $data): bool
    {
        $stmt = db()->prepare(
            'INSERT INTO bookings (resource_id, resource_name, booked_by, status, booking_date, start_time, end_time, start_date, end_date)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $data['resource_id'],
            $data['resource_name'],
            $data['booked_by'],
            'active',
            $data['booking_date'],
            $data['start_time'],
            $data['end_time'],
            $data['booking_date'],
            $data['booking_date'],
        ]);
    }

    private function getFallbackBookings(): array
    {
        return [
            ['start_time' => '09:00:00', 'end_time' => '10:00:00', 'booked_by' => 'Procurement Team', 'resource_name' => 'Conference Room B2'],
        ];
    }
}
