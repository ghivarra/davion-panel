<?php

namespace App\Controllers\Admin;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\AdminMenuGroup;
use App\Models\AdminMenuModel;
use Config\Services;

class MenuController extends BaseController
{
    public function list()
    {
        $permission = $this->checkPermission('menuView');
        
        if (!$permission)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
        }

        // get instance
        $orm = new AdminMenuModel();

        // get

    }

    //================================================================================================
}
