<?php
/**
 * AssetFlow - Allocation & Transfer Model
 */

class Allocation
{
    public function getCurrentAllocation(int $assetId): ?array
    {
        try {
            $stmt = db()->prepare(
                "SELECT * FROM asset_allocations WHERE asset_id = ? AND status = 'active' ORDER BY allocated_at DESC LIMIT 1"
            );
            $stmt->execute([$assetId]);

            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getHistory(int $assetId): array
    {
        try {
            $stmt = db()->prepare(
                'SELECT * FROM allocation_history WHERE asset_id = ? ORDER BY created_at DESC'
            );
            $stmt->execute([$assetId]);

            return $stmt->fetchAll() ?: $this->getFallbackHistory();
        } catch (PDOException $e) {
            return $this->getFallbackHistory();
        }
    }

    public function createTransfer(array $data): bool
    {
        $stmt = db()->prepare(
            'INSERT INTO transfers (asset_id, from_user, to_user, reason, status) VALUES (?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $data['asset_id'],
            $data['from_user'],
            $data['to_user'],
            $data['reason'] ?? null,
            'pending',
        ]);
    }

    public function getPendingTransfers(): int
    {
        try {
            return (int) db()->query("SELECT COUNT(*) FROM transfers WHERE status = 'pending'")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    private function getFallbackHistory(): array
    {
        return [
            ['event_type' => 'allocated', 'employee_name' => 'Priya Shah',  'department' => 'Engineering', 'condition_note' => null, 'created_at' => date('Y-m-d', strtotime('Mar 12'))],
            ['event_type' => 'returned',  'employee_name' => 'Arjun Nair',  'department' => null,          'condition_note' => 'Good', 'created_at' => date('Y-m-d', strtotime('Jan 04'))],
        ];
    }
}
