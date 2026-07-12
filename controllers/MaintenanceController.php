<?php
/**
 * AssetFlow - Maintenance Controller
 */

class MaintenanceController
{
    private Maintenance $maintenanceModel;

    public function __construct()
    {
        $this->maintenanceModel = new Maintenance();
    }

    public function index(): void
    {
        requireAuth();

        if (($_GET['action'] ?? '') === 'updateStage' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateStage();
            return;
        }

        render('maintenance/index', [
            'pageTitle'   => 'Maintenance Management',
            'currentPage' => 'maintenance',
            'kanban'      => $this->maintenanceModel->getByStage(),
            'stages'      => $this->maintenanceModel->getStages(),
            'flash'       => getFlash(),
            'user'        => currentUser(),
        ]);
    }

    private function updateStage(): void
    {
        requireRole('admin');

        $id    = (int) ($_POST['id'] ?? 0);
        $stage = $_POST['stage'] ?? '';

        if ($id && $this->maintenanceModel->updateStage($id, $stage)) {
            logActivity('maintenance', 'Maintenance request moved to ' . $stage, 'bi-tools', 'Approval');
            setFlash('success', 'Stage updated.');
        } else {
            setFlash('error', 'Failed to update stage.');
        }

        redirect('maintenance');
    }
}
