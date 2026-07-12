<?php
/**
 * AssetFlow - Asset Controller
 */

class AssetController
{
    private Asset $assetModel;

    public function __construct()
    {
        $this->assetModel = new Asset();
    }

    public function index(): void
    {
        requireAuth();

        $action = $_GET['action'] ?? 'list';

        match ($action) {
            'register' => $this->showRegisterForm(),
            'store'    => $this->store(),
            default    => $this->list(),
        };
    }

    private function list(): void
    {
        $filters = [
            'q'          => trim($_GET['q'] ?? ''),
            'category'   => $_GET['category'] ?? '',
            'status'     => $_GET['status'] ?? '',
            'department' => $_GET['department'] ?? '',
        ];

        render('assets/index', [
            'pageTitle'   => 'Asset Directory',
            'currentPage' => 'assets',
            'assets'      => $this->assetModel->search($filters),
            'categories'  => $this->assetModel->getCategories(),
            'departments' => $this->assetModel->getDepartments(),
            'filters'     => $filters,
            'flash'       => getFlash(),
            'user'        => currentUser(),
        ]);
    }

    private function showRegisterForm(): void
    {
        requireRole('admin');

        render('assets/register', [
            'pageTitle'   => 'Register Asset',
            'currentPage' => 'assets',
            'categories'  => $this->assetModel->getCategories(),
            'departments' => $this->assetModel->getDepartments(),
            'vendors'     => $this->assetModel->getVendors(),
            'flash'       => getFlash(),
            'user'        => currentUser(),
        ]);
    }

    private function store(): void
    {
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('assets', ['action' => 'register']);
        }

        $assetCode = trim($_POST['asset_code'] ?? '');
        $name      = trim($_POST['name'] ?? '');

        if ($assetCode === '' || $name === '') {
            setFlash('error', 'Asset tag and name are required.');
            redirect('assets', ['action' => 'register']);
        }

        $photoPath = null;
        if (!empty($_FILES['photo']['name'])) {
            $uploadDir = APP_ROOT . '/uploads/assets/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = $assetCode . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename)) {
                $photoPath = 'uploads/assets/' . $filename;
            }
        }

        $categoryName = '';
        foreach ($this->assetModel->getCategories() as $cat) {
            if ((string) $cat['id'] === ($_POST['category_id'] ?? '')) {
                $categoryName = $cat['name'];
                break;
            }
        }

        $data = [
            'asset_code'     => $assetCode,
            'name'           => $name,
            'asset_type'     => $categoryName ?: 'General',
            'serial_number'  => trim($_POST['serial_number'] ?? ''),
            'category_id'    => (int) ($_POST['category_id'] ?? 0) ?: null,
            'department_id'  => (int) ($_POST['department_id'] ?? 0) ?: null,
            'vendor_id'      => (int) ($_POST['vendor_id'] ?? 0) ?: null,
            'purchase_date'  => $_POST['purchase_date'] ?? null,
            'warranty_until' => $_POST['warranty_until'] ?? null,
            'location'       => trim($_POST['location'] ?? ''),
            'condition_note' => $_POST['condition_note'] ?? 'Good',
            'qr_code'        => trim($_POST['qr_code'] ?? '') ?: 'QR-' . $assetCode,
            'photo_path'     => $photoPath,
            'status'         => $_POST['status'] ?? 'available',
        ];

        try {
            if ($this->assetModel->create($data)) {
                logActivity('asset', "Asset {$assetCode} registered: {$name}", 'bi-box-seam', 'Alert');
                setFlash('success', 'Asset registered successfully.');
                redirect('assets');
            }
        } catch (PDOException $e) {
            setFlash('error', 'Failed to register asset. Tag may already exist.');
        }

        redirect('assets', ['action' => 'register']);
    }
}
