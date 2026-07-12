<?php
/**
 * AssetFlow - Organization Setup Controller
 */

class OrganizationController
{
    public function index(): void
    {
        requireAuth();
        requireRole('admin');

        $assetModel = new Asset();

        render('organization/index', [
            'pageTitle'   => 'Organization Setup',
            'currentPage' => 'organization-setup',
            'departments' => $assetModel->getDepartments(),
            'categories'  => $assetModel->getCategories(),
            'vendors'     => $assetModel->getVendors(),
            'flash'       => getFlash(),
            'user'        => currentUser(),
        ]);
    }
}
