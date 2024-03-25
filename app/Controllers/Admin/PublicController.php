<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Ghivarra\DavionShield;
use App\Models\WebsiteModel;
use App\Models\AdminMenuModel;
use CodeIgniter\HTTP\ResponseInterface;

class PublicController extends BaseController
{
    public function singlePageApplication(): ResponseInterface | string
    {
        if ($this->request->isAjax())
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Halaman tidak ditemukan',
            ])->setStatusCode(404);
        }

        $websiteModel = new WebsiteModel();

        // return and render vue
        return $this->vue->render('Panel/PanelIndexView', [
            'title'   => $_ENV['VITE_APP_NAME'],
            'website' => $websiteModel->getAllData()
        ]);
    }

    //================================================================================================

    public function sessionData(): ResponseInterface
    {
        $davionShield = new DavionShield();
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diambil',
            'data'    => $davionShield->getAccountData()
        ]);
    }

    //================================================================================================

    public function logout(): ResponseInterface
    {
        $davionShield = new DavionShield();
        $davionShield->logout();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Anda berhasil keluar/logout dari panel',
        ]);
    }

    //================================================================================================

    public function menu(): ResponseInterface
    {
        $davionShield = new DavionShield();
        $accountData  = $davionShield->getAccountData();
        
        // check if superadmin
        if ($accountData['is_superadmin'] === SUPERADMIN)
        {
            $adminMenuModel = new AdminMenuModel();
            $adminMenu      = $adminMenuModel->getSuperadminMenu();

        } else {

            $adminMenuListModel = new AdminMenuListModel();
            $adminMenu          = $adminMenuListModel->getRoleMenu($accountData['admin_role_id']);
        }

        /// return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $adminMenu
        ]);
    }

    //================================================================================================
}
