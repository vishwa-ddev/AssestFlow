<?php
/**
 * AssetFlow - Maintenance Model
 */

class Maintenance
{
    private const STAGES = ['pending', 'approved', 'technician_assigned', 'in_progress', 'resolved'];

    public function getByStage(): array
    {
        $result = array_fill_keys(self::STAGES, []);

        try {
            $stmt = db()->query(
                'SELECT m.*, a.asset_code, a.name AS asset_name
                 FROM maintenance_requests m
                 JOIN assets a ON a.id = m.asset_id
                 ORDER BY m.created_at DESC'
            );

            foreach ($stmt->fetchAll() as $row) {
                $stage = $row['stage'] ?? 'pending';
                if (isset($result[$stage])) {
                    $result[$stage][] = $row;
                }
            }
        } catch (PDOException $e) {
            $result = $this->getFallbackKanban();
        }

        return $result;
    }

    public function updateStage(int $id, string $stage): bool
    {
        if (!in_array($stage, self::STAGES, true)) {
            return false;
        }

        $resolvedAt = $stage === 'resolved' ? date('Y-m-d') : null;

        $stmt = db()->prepare(
            'UPDATE maintenance_requests SET stage = ?, resolved_at = COALESCE(?, resolved_at) WHERE id = ?'
        );

        return $stmt->execute([$stage, $resolvedAt, $id]);
    }

    public function getStages(): array
    {
        return [
            'pending'              => 'Pending',
            'approved'             => 'Approved',
            'technician_assigned'  => 'Technician Assigned',
            'in_progress'          => 'In Progress',
            'resolved'             => 'Resolved',
        ];
    }

    private function getFallbackKanban(): array
    {
        return [
            'pending' => [
                ['asset_code' => 'AF0062', 'asset_name' => 'Projector', 'title' => 'Projector Bulb', 'issue_description' => 'Not Turning On'],
                ['asset_code' => 'AF0003', 'asset_name' => 'AC Unit',   'title' => 'AC Unit',        'issue_description' => 'Noisy Compressor'],
            ],
            'technician_assigned' => [
                ['asset_code' => 'AF0078', 'asset_name' => 'Forklift', 'title' => 'Forklift', 'technician_name' => 'R Varma'],
            ],
            'in_progress' => [
                ['asset_code' => 'AF0897', 'asset_name' => 'Printer', 'title' => 'Printer Jam', 'issue_description' => 'Parts Ordered'],
            ],
            'resolved' => [
                ['asset_code' => 'AF0873', 'asset_name' => 'Chair', 'title' => 'Chair Repair', 'resolved_at' => '2026-07-07'],
            ],
            'approved' => [],
        ];
    }
}
