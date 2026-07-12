<?php
/**
 * AssetFlow - Dashboard Controller
 * Handles the admin dashboard and overview data.
 */

class DashboardController
{
    private Dashboard $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new Dashboard();
    }

    /**
     * Display the main admin dashboard.
     */
    public function index(): void
    {
        requireAuth();

        $kpis            = $this->dashboardModel->getKpiStats();
        $overdueCount    = $this->dashboardModel->getOverdueReturnsCount();
        $recentActivity  = $this->dashboardModel->getRecentActivity();
        $flash           = getFlash();

        render('dashboard/index', [
            'pageTitle'      => "Today's Overview",
            'currentPage'    => 'dashboard',
            'kpis'           => $kpis,
            'overdueCount'   => $overdueCount,
            'recentActivity' => $recentActivity,
            'flash'          => $flash,
            'user'           => currentUser(),
        ]);
    }
}
