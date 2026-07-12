<?php
/**
 * AssetFlow - Reports Controller
 */

class ReportController
{
    private Report $reportModel;

    public function __construct()
    {
        $this->reportModel = new Report();
    }

    public function index(): void
    {
        requireAuth();

        if (($_GET['action'] ?? '') === 'export') {
            $this->export();
            return;
        }

        render('reports/index', [
            'pageTitle'      => 'Reports & Analytics',
            'currentPage'    => 'reports',
            'mostUsed'       => $this->reportModel->getMostUsedAssets(),
            'idleAssets'     => $this->reportModel->getIdleAssets(),
            'maintenanceDue' => $this->reportModel->getMaintenanceDue(),
            'utilization'    => $this->reportModel->getUtilizationByDepartment(),
            'flash'          => getFlash(),
            'user'           => currentUser(),
        ]);
    }

    private function export(): void
    {
        requireRole('admin');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="assetflow-report-' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Report', 'Item', 'Detail']);

        foreach ($this->reportModel->getMostUsedAssets() as $item) {
            fputcsv($out, ['Most Used', $item['name'], $item['count']]);
        }
        foreach ($this->reportModel->getIdleAssets() as $item) {
            fputcsv($out, ['Idle Assets', $item['name'], $item['detail']]);
        }
        foreach ($this->reportModel->getMaintenanceDue() as $item) {
            fputcsv($out, ['Maintenance Due', $item['name'], $item['detail']]);
        }

        fclose($out);
        exit;
    }
}
