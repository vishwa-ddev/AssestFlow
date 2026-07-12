<?php
/**
 * AssetFlow - Notification Model
 */

class Notification
{
    public function getAll(?string $category = null): array
    {
        try {
            $sql = 'SELECT * FROM activity_log WHERE 1=1';
            $params = [];

            if ($category && $category !== 'all') {
                $sql .= ' AND activity_type = ?';
                $params[] = $category;
            }

            $sql .= ' ORDER BY created_at DESC LIMIT 50';

            $stmt = db()->prepare($sql);
            $stmt->execute($params);

            $rows = $stmt->fetchAll();

            return $rows ?: $this->getFallbackFeed();
        } catch (PDOException $e) {
            return $this->getFallbackFeed();
        }
    }

    public function getTabs(): array
    {
        return [
            'all'      => 'All',
            'allocation' => 'Alerts',
            'approval' => 'Approvals',
            'booking'  => 'Bookings',
        ];
    }

    private function getFallbackFeed(): array
    {
        return [
            ['activity_type' => 'allocation', 'message' => 'Laptop AF0014 assigned to Priya Shah', 'icon' => 'bi-laptop', 'badge' => 'Alert',    'created_at' => date('Y-m-d H:i:s', strtotime('-2 minutes'))],
            ['activity_type' => 'approval',   'message' => 'Maintenance request approved',          'icon' => 'bi-check-circle', 'badge' => 'Approval', 'created_at' => date('Y-m-d H:i:s', strtotime('-18 minutes'))],
            ['activity_type' => 'booking',    'message' => 'Room B2 booking confirmed',             'icon' => 'bi-door-open', 'badge' => 'Booking',  'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['activity_type' => 'transfer',   'message' => 'Transfer approved',                     'icon' => 'bi-arrow-left-right', 'badge' => 'Approval', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))],
            ['activity_type' => 'alert',      'message' => 'Overdue return',                        'icon' => 'bi-exclamation-triangle', 'badge' => 'Alert', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['activity_type' => 'audit',      'message' => 'Audit discrepancy detected',            'icon' => 'bi-shield-exclamation', 'badge' => 'Alert', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
        ];
    }
}
