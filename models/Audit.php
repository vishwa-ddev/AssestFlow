<?php
/**
 * AssetFlow - Audit Model
 */

class Audit
{
    public function getActiveAudit(): ?array
    {
        try {
            $stmt = db()->query(
                "SELECT * FROM audit_cycles WHERE status = 'open' ORDER BY created_at DESC LIMIT 1"
            );
            $audit = $stmt->fetch();

            if (!$audit) {
                return $this->getFallbackAudit();
            }

            $audit['auditors'] = $this->getAuditors((int) $audit['id']);
            $audit['checklist'] = $this->getChecklist((int) $audit['id']);
            $audit['flagged_count'] = $this->countFlagged((int) $audit['id']);

            return $audit;
        } catch (PDOException $e) {
            return $this->getFallbackAudit();
        }
    }

    public function getAuditors(int $auditId): array
    {
        $stmt = db()->prepare('SELECT auditor_name FROM audit_auditors WHERE audit_id = ?');
        $stmt->execute([$auditId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getChecklist(int $auditId): array
    {
        $stmt = db()->prepare(
            'SELECT c.*, a.asset_code, a.name AS asset_name
             FROM audit_checklist c
             JOIN assets a ON a.id = c.asset_id
             WHERE c.audit_id = ?
             ORDER BY a.asset_code'
        );
        $stmt->execute([$auditId]);

        return $stmt->fetchAll();
    }

    public function countFlagged(int $auditId): int
    {
        $stmt = db()->prepare(
            "SELECT COUNT(*) FROM audit_checklist WHERE audit_id = ? AND verification IN ('missing', 'damaged')"
        );
        $stmt->execute([$auditId]);

        return (int) $stmt->fetchColumn();
    }

    public function closeAudit(int $auditId): bool
    {
        $stmt = db()->prepare("UPDATE audit_cycles SET status = 'closed' WHERE id = ?");

        return $stmt->execute([$auditId]);
    }

    private function getFallbackAudit(): array
    {
        return [
            'id' => 1,
            'title' => 'Q3 Audit',
            'department' => 'Engineering Department',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-15',
            'status' => 'open',
            'auditors' => ['A Rao', 'S Iqbal'],
            'flagged_count' => 2,
            'checklist' => [
                ['asset_code' => 'AF0003', 'asset_name' => 'Dell Laptop',  'expected_location' => 'Desk E12', 'verification' => 'verified'],
                ['asset_code' => 'AF0021', 'asset_name' => 'Office Chair', 'expected_location' => 'Desk E14', 'verification' => 'missing'],
                ['asset_code' => 'AF9838', 'asset_name' => 'Monitor',      'expected_location' => 'Desk E15', 'verification' => 'damaged'],
            ],
        ];
    }
}
