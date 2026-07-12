<?php
/**
 * AssetFlow - Notifications Controller
 */

class NotificationController
{
    private Notification $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    public function index(): void
    {
        requireAuth();

        $tab = $_GET['tab'] ?? 'all';

        render('notifications/index', [
            'pageTitle'   => 'Activity Logs & Notifications',
            'currentPage' => 'notifications',
            'tabs'        => $this->notificationModel->getTabs(),
            'activeTab'   => $tab,
            'feed'        => $this->notificationModel->getAll($tab === 'all' ? null : $tab),
            'flash'       => getFlash(),
            'user'        => currentUser(),
        ]);
    }
}
