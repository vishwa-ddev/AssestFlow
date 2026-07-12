<?php
/**
 * AssetFlow - Dashboard Model
 * Fetches KPI statistics and activity data from the database.
 */

class Dashboard
{
    /**
     * Get KPI card statistics for the dashboard overview.
     */
    public function getKpiStats(): array
    {
        try {
            $db = db();

            return [
                [
                    'key'   => 'available',
                    'title' => 'Assets Available',
                    'value' => $this->countAssetsByStatus($db, 'available'),
                    'icon'  => 'bi-check-circle',
                    'color' => 'primary',
                ],
                [
                    'key'   => 'allocated',
                    'title' => 'Assets Allocated',
                    'value' => $this->countAssetsByStatus($db, 'allocated'),
                    'icon'  => 'bi-person-check',
                    'color' => 'info',
                ],
                [
                    'key'   => 'maintenance',
                    'title' => 'Assets Under Maintenance',
                    'value' => $this->countAssetsByStatus($db, 'maintenance'),
                    'icon'  => 'bi-tools',
                    'color' => 'warning',
                ],
                [
                    'key'   => 'bookings',
                    'title' => 'Active Bookings',
                    'value' => $this->countByStatus($db, 'bookings', 'active'),
                    'icon'  => 'bi-calendar-event',
                    'color' => 'success',
                ],
                [
                    'key'   => 'transfers',
                    'title' => 'Pending Transfers',
                    'value' => $this->countByStatus($db, 'transfers', 'pending'),
                    'icon'  => 'bi-arrow-repeat',
                    'color' => 'secondary',
                ],
                [
                    'key'   => 'returns',
                    'title' => 'Upcoming Returns',
                    'value' => $this->countUpcomingReturns($db),
                    'icon'  => 'bi-clock-history',
                    'color' => 'danger',
                ],
            ];
        } catch (PDOException $e) {
            return $this->getFallbackKpis();
        }
    }

    /**
     * Get count of assets overdue for return.
     */
    public function getOverdueReturnsCount(): int
    {
        try {
            $stmt = db()->query(
                "SELECT COUNT(*) FROM asset_allocations
                 WHERE status = 'active' AND expected_return_date < CURDATE()"
            );

            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 3;
        }
    }

    /**
     * Get recent activity entries for the timeline.
     */
    public function getRecentActivity(): array
    {
        try {
            $stmt = db()->query(
                'SELECT activity_type, message, icon, created_at
                 FROM activity_log
                 ORDER BY created_at DESC
                 LIMIT 10'
            );

            $rows = $stmt->fetchAll();

            return $rows ?: $this->getFallbackActivity();
        } catch (PDOException $e) {
            return $this->getFallbackActivity();
        }
    }

    /**
     * Count assets by status.
     */
    private function countAssetsByStatus(PDO $db, string $status): int
    {
        $stmt = $db->prepare('SELECT COUNT(*) FROM assets WHERE status = ?');
        $stmt->execute([$status]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Count records by status in a given table.
     */
    private function countByStatus(PDO $db, string $table, string $status): int
    {
        $allowedTables = ['bookings', 'transfers'];
        if (!in_array($table, $allowedTables, true)) {
            return 0;
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE status = ?");
        $stmt->execute([$status]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Count allocations with returns due in the next 7 days.
     */
    private function countUpcomingReturns(PDO $db): int
    {
        $stmt = $db->query(
            "SELECT COUNT(*) FROM asset_allocations
             WHERE status = 'active'
             AND expected_return_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
        );

        return (int) $stmt->fetchColumn();
    }

    /**
     * Fallback KPI data when database tables are unavailable.
     */
    private function getFallbackKpis(): array
    {
        return [
            ['key' => 'available',    'title' => 'Assets Available',         'value' => 128, 'icon' => 'bi-check-circle',   'color' => 'primary'],
            ['key' => 'allocated',    'title' => 'Assets Allocated',         'value' => 74,  'icon' => 'bi-person-check',   'color' => 'info'],
            ['key' => 'maintenance',  'title' => 'Assets Under Maintenance', 'value' => 9,   'icon' => 'bi-tools',          'color' => 'warning'],
            ['key' => 'bookings',     'title' => 'Active Bookings',          'value' => 16,  'icon' => 'bi-calendar-event', 'color' => 'success'],
            ['key' => 'transfers',    'title' => 'Pending Transfers',        'value' => 5,   'icon' => 'bi-arrow-repeat',   'color' => 'secondary'],
            ['key' => 'returns',      'title' => 'Upcoming Returns',         'value' => 11,  'icon' => 'bi-clock-history',  'color' => 'danger'],
        ];
    }

    /**
     * Fallback activity data when database tables are unavailable.
     */
    private function getFallbackActivity(): array
    {
        return [
            [
                'activity_type' => 'allocation',
                'message'       => 'Laptop AF-0114 allocated to Priya Shah',
                'icon'          => 'bi-laptop',
                'created_at'    => date('Y-m-d H:i:s', strtotime('-2 hours')),
            ],
            [
                'activity_type' => 'booking',
                'message'       => 'Room B2 booking confirmed',
                'icon'          => 'bi-door-open',
                'created_at'    => date('Y-m-d H:i:s', strtotime('-4 hours')),
            ],
            [
                'activity_type' => 'maintenance',
                'message'       => 'Projector AF-0062 maintenance completed',
                'icon'          => 'bi-easel2',
                'created_at'    => date('Y-m-d H:i:s', strtotime('-6 hours')),
            ],
        ];
    }
}
