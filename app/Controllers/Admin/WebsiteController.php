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

    public function mainFormUpdate(): ResponseInterface
    {
        $this->moduleAlias = 'websiteMainFormUpdate';
        $permission        = $this->checkPermission($this->moduleAlias);
        
        if (!$permission)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
        }

        // get post data
        $input = [
            [
                'name' => 'name', 
                'value' => $this->request->getPost('name')
            ], [
                'name' => 'tagline', 
                'value' => $this->request->getPost('tagline')
            ], [
                'name' => 'app_version', 
                'value' => $this->request->getPost('app_version')
            ], [
                'name' => 'description', 
                'value' => $this->request->getPost('description')
            ]
        ];

        // update
        $websiteModel = new WebsiteModel();
        $websiteModel->updateBatch($input, 'name');

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diperbaharui'
        ]);
    }

    //================================================================================================
}
