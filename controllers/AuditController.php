<?php
/**
 * AssetFlow - Audit Controller
 */

class AuditController
{
    private Audit $auditModel;

    public function __construct()
    {
        $this->auditModel = new Audit();
    }

    public function index(): void
    {
        requireAuth();

        if (($_GET['action'] ?? '') === 'close' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->closeAudit();
            return;
        }

        $audit = $this->auditModel->getActiveAudit();

        render('audit/index', [
            'pageTitle'   => 'Audit Management',
            'currentPage' => 'audit',
            'audit'       => $audit,
            'flash'       => getFlash(),
            'user'        => currentUser(),
        ]);
    }

    private function closeAudit(): void
    {
        requireRole('admin');

        $auditId = (int) ($_POST['audit_id'] ?? 0);

        if ($auditId && $this->auditModel->closeAudit($auditId)) {
            logActivity('audit', 'Audit cycle closed', 'bi-shield-check', 'Approval');
            setFlash('success', 'Audit cycle closed successfully.');
        } else {
            setFlash('error', 'Failed to close audit cycle.');
        }

        redirect('audit');
    }
}
