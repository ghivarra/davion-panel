<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WebsiteModel;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;

class WebsiteController extends BaseController
{
    protected $moduleAlias;

    //================================================================================================

    public function data(): ResponseInterface
    {
        $this->moduleAlias = 'websiteData';
        $permission        = $this->checkPermission($this->moduleAlias);
        
        if (!$permission)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
        }

        // get data
        $websiteModel = new WebsiteModel();

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $websiteModel->getAllData()
        ]);
    }

    //================================================================================================
}
