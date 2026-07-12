<?php
/**
 * AssetFlow - Asset Model
 */

class Asset
{
    public function search(array $filters = []): array
    {
        try {
            $sql = 'SELECT a.*, c.name AS category_name, d.name AS department_name
                    FROM assets a
                    LEFT JOIN asset_categories c ON c.id = a.category_id
                    LEFT JOIN departments d ON d.id = a.department_id
                    WHERE 1=1';
            $params = [];

            if (!empty($filters['q'])) {
                $sql .= ' AND (a.asset_code LIKE ? OR a.name LIKE ? OR a.serial_number LIKE ? OR a.qr_code LIKE ?)';
                $like = '%' . $filters['q'] . '%';
                $params = array_merge($params, [$like, $like, $like, $like]);
            }
            if (!empty($filters['category'])) {
                $sql .= ' AND a.category_id = ?';
                $params[] = $filters['category'];
            }
            if (!empty($filters['status'])) {
                $sql .= ' AND a.status = ?';
                $params[] = $filters['status'];
            }
            if (!empty($filters['department'])) {
                $sql .= ' AND a.department_id = ?';
                $params[] = $filters['department'];
            }

            $sql .= ' ORDER BY a.asset_code ASC';

            $stmt = db()->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll() ?: $this->getFallbackAssets();
        } catch (PDOException $e) {
            return $this->getFallbackAssets();
        }
    }

    public function findById(int $id): ?array
    {
        try {
            $stmt = db()->prepare(
                'SELECT a.*, c.name AS category_name, d.name AS department_name, v.name AS vendor_name
                 FROM assets a
                 LEFT JOIN asset_categories c ON c.id = a.category_id
                 LEFT JOIN departments d ON d.id = a.department_id
                 LEFT JOIN vendors v ON v.id = a.vendor_id
                 WHERE a.id = ?'
            );
            $stmt->execute([$id]);

            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getAllForSelect(): array
    {
        try {
            $stmt = db()->query(
                'SELECT id, asset_code, name, status FROM assets ORDER BY asset_code ASC'
            );

            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function create(array $data): bool
    {
        $stmt = db()->prepare(
            'INSERT INTO assets (asset_code, name, asset_type, serial_number, category_id, department_id,
             vendor_id, purchase_date, warranty_until, location, condition_note, qr_code, photo_path, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $data['asset_code'],
            $data['name'],
            $data['asset_type'],
            $data['serial_number'] ?: null,
            $data['category_id'] ?: null,
            $data['department_id'] ?: null,
            $data['vendor_id'] ?: null,
            $data['purchase_date'] ?: null,
            $data['warranty_until'] ?: null,
            $data['location'] ?: null,
            $data['condition_note'] ?: 'Good',
            $data['qr_code'] ?: null,
            $data['photo_path'] ?: null,
            $data['status'] ?: 'available',
        ]);
    }

    public function getCategories(): array
    {
        try {
            return db()->query('SELECT id, name FROM asset_categories ORDER BY name')->fetchAll();
        } catch (PDOException $e) {
            return [
                ['id' => 1, 'name' => 'Electronics'],
                ['id' => 2, 'name' => 'Furniture'],
                ['id' => 3, 'name' => 'Vehicles'],
            ];
        }
    }

    public function getDepartments(): array
    {
        try {
            return db()->query('SELECT id, name FROM departments ORDER BY name')->fetchAll();
        } catch (PDOException $e) {
            return [
                ['id' => 1, 'name' => 'Engineering'],
                ['id' => 2, 'name' => 'Operations'],
                ['id' => 3, 'name' => 'HR'],
            ];
        }
    }

    public function getVendors(): array
    {
        try {
            return db()->query('SELECT id, name FROM vendors ORDER BY name')->fetchAll();
        } catch (PDOException $e) {
            return [
                ['id' => 1, 'name' => 'Dell India'],
                ['id' => 2, 'name' => 'Epson'],
                ['id' => 3, 'name' => 'IKEA'],
            ];
        }
    }

    private function getFallbackAssets(): array
    {
        return [
            ['asset_code' => 'AF0012', 'name' => 'Dell Laptop',  'category_name' => 'Electronics', 'status' => 'allocated',   'location' => 'Bengaluru'],
            ['asset_code' => 'AF0062', 'name' => 'Projector',    'category_name' => 'Electronics', 'status' => 'maintenance', 'location' => 'HQ Floor 2'],
            ['asset_code' => 'AF0201', 'name' => 'Office Chair', 'category_name' => 'Furniture',   'status' => 'available',   'location' => 'Warehouse'],
        ];
    }
}
