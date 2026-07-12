<?php
/**
 * AssetFlow - Allocation & Transfer Controller
 */

class AllocationController
{
    private Asset $assetModel;
    private Allocation $allocationModel;

    public function __construct()
    {
        $this->assetModel      = new Asset();
        $this->allocationModel = new Allocation();
    }

    public function index(): void
    {
        requireAuth();

        $action = $_GET['action'] ?? 'list';

        if ($action === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->submitTransfer();
            return;
        }

        $assetId    = (int) ($_GET['asset_id'] ?? $_POST['asset_id'] ?? 0);
        $assets     = $this->assetModel->getAllForSelect();
        $allocation = $assetId ? $this->allocationModel->getCurrentAllocation($assetId) : null;
        $history    = $assetId ? $this->allocationModel->getHistory($assetId) : [];
        $selected   = $assetId ? $this->assetModel->findById($assetId) : null;

        render('allocation-transfer/index', [
            'pageTitle'   => 'Asset Allocation & Transfer',
            'currentPage' => 'allocation-transfer',
            'assets'      => $assets,
            'assetId'     => $assetId,
            'selected'    => $selected,
            'allocation'  => $allocation,
            'history'     => $history,
            'flash'       => getFlash(),
            'user'        => currentUser(),
        ]);
    }

    private function submitTransfer(): void
    {
        $assetId  = (int) ($_POST['asset_id'] ?? 0);
        $fromUser = trim($_POST['from_user'] ?? '');
        $toUser   = trim($_POST['to_user'] ?? '');
        $reason   = trim($_POST['reason'] ?? '');

        if (!$assetId || $fromUser === '' || $toUser === '') {
            setFlash('error', 'Please fill in all required fields.');
            redirect('allocation-transfer', ['asset_id' => $assetId]);
        }

        $allocation = $this->allocationModel->getCurrentAllocation($assetId);
        if (!$allocation) {
            setFlash('error', 'Asset is not currently allocated. Direct allocation is not supported here.');
            redirect('allocation-transfer', ['asset_id' => $assetId]);
        }

        if ($this->allocationModel->createTransfer([
            'asset_id'  => $assetId,
            'from_user' => $fromUser,
            'to_user'   => $toUser,
            'reason'    => $reason,
        ])) {
            $asset = $this->assetModel->findById($assetId);
            $code  = $asset['asset_code'] ?? 'Asset';
            logActivity('transfer', "Transfer request submitted for {$code}", 'bi-arrow-left-right', 'Approval');
            setFlash('success', 'Transfer request submitted successfully.');
        } else {
            setFlash('error', 'Failed to submit transfer request.');
        }

        redirect('allocation-transfer', ['asset_id' => $assetId]);
    }
}
