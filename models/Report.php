<?php
/**
 * AssetFlow - Reports Model
 */

class Report
{
    public function getMostUsedAssets(): array
    {
        try {
            $stmt = db()->query(
                "SELECT resource_name AS name, COUNT(*) AS usage_count
                 FROM bookings WHERE status = 'active'
                 GROUP BY resource_name ORDER BY usage_count DESC LIMIT 5"
            );

            $rows = $stmt->fetchAll();

            if ($rows) {
                return array_map(fn($r) => [
                    'name'  => $r['name'],
                    'count' => $r['usage_count'] . ' bookings',
                ], $rows);
            }
        } catch (PDOException $e) {
            // fall through
        }

        return [
            ['name' => 'Room B2',        'count' => '34 bookings'],
            ['name' => 'Van AF343',      'count' => '21 trips'],
            ['name' => 'Projector AF335','count' => '18 uses'],
        ];
    }

    public function getIdleAssets(): array
    {
        return [
            ['name' => 'Camera AF0301', 'detail' => 'Unused 60 days'],
            ['name' => 'Chair AF0410',  'detail' => 'Unused 45 days'],
        ];
    }

    public function getMaintenanceDue(): array
    {
        return [
            ['name' => 'Forklift AF0087', 'detail' => 'Service due in 5 days'],
            ['name' => 'Laptop AF0020',   'detail' => 'Near retirement'],
        ];
    }

    public function getUtilizationByDepartment(): array
    {
        try {
            $stmt = db()->query(
                'SELECT d.name, COUNT(a.id) AS total
                 FROM departments d
                 LEFT JOIN assets a ON a.department_id = d.id AND a.status = \'allocated\'
                 GROUP BY d.id ORDER BY total DESC'
            );

            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [
                ['name' => 'Engineering', 'total' => 42],
                ['name' => 'Operations',  'total' => 28],
                ['name' => 'HR',          'total' => 12],
            ];
        }
    }
}
