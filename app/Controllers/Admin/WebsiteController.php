<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Chrisbliss18\PHP_ICO;
use App\Models\WebsiteModel;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;

class WebsiteController extends BaseController
{
    public function data(): ResponseInterface
    {
        $permission = $this->checkPermission('websiteView');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // session not needed anymore, unlock the session file mechanism
        session_write_close();

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

    public function iconUpdate(): ResponseInterface
    {
        $permission = $this->checkPermission('websiteUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate file
        $validation = $this->validate([
            'icon' => 'uploaded[icon]|max_size[icon,4096]|is_image[icon]|mime_in[icon,image/png]'
        ]);

        if (!$validation)
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data gagal diperbaharui',
                'reasons' => $this->validator->getErrors()
            ]);
        }

        // get file
        $file = $this->request->getFile('icon');

        // store original file with new name
        $newName = $file->getRandomName();
        $newPath = IMAGEPATH . "icon/{$newName}";
        $file->move(IMAGEPATH . 'icon', $newName);

        // create model instance
        $websiteModel = new WebsiteModel();

        // get logo version
        $data    = $websiteModel->select('value')->where('name', 'icon_version')->first();
        $version = explode('.', $data['value']);
        $version = array_map('intval', $version);

        // update version
        if ($version[2] >= 10)
        {
            $version[2] = 0;
            $version[1]++;
            
            if ($version[1] >= 10)
            {
                $version[1] = 0;
                $version[0]++;
            }

        } else {

            $version[2]++;
        }

        // delete old favicon and create new favicon
        $faviconPath = FCPATH . 'favicon.ico';

        if (file_exists($faviconPath) && is_really_writable($faviconPath))
        {
            unlink($faviconPath);
        }

        $phpIco = new PHP_ICO($newPath, [[32, 32]]);
        $phpIco->save_ico($faviconPath);

        // update
        $websiteModel->updateBatch([
            [
                'name' => 'icon',
                'value' => $newName 
            ], [
                'name' => 'icon_version',
                'value' => implode('.', $version)
            ]
        ], 'name');

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diperbaharui'
        ]);
    }

    //================================================================================================

    public function logoUpdate(): ResponseInterface
    {
        $permission = $this->checkPermission('websiteUpdate');
        
        if (!$permission)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
        }

        // validate file
        $validation = $this->validate([
            'logo' => 'uploaded[logo]|max_size[logo,4096]|is_image[logo]|mime_in[logo,image/png]'
        ]);

        if (!$validation)
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data gagal diperbaharui',
                'reasons' => $this->validator->getErrors()
            ]);
        }

        // get file
        $file = $this->request->getFile('logo');

        // store original file with new name
        $newName = $file->getRandomName();
        $newPath = IMAGEPATH . "logo/{$newName}";
        $file->move(IMAGEPATH . 'logo', $newName);

        // create model instance
        $websiteModel = new WebsiteModel();

        // get logo version
        $data    = $websiteModel->select('value')->where('name', 'logo_version')->first();
        $version = explode('.', $data['value']);
        $version = array_map('intval', $version);

        // update version
        if ($version[2] >= 10)
        {
            $version[2] = 0;
            $version[1]++;
            
            if ($version[1] >= 10)
            {
                $version[1] = 0;
                $version[0]++;
            }

        } else {

            $version[2]++;
        }

        // update
        $websiteModel->updateBatch([
            [
                'name' => 'logo',
                'value' => $newName 
            ], [
                'name' => 'logo_version',
                'value' => implode('.', $version)
            ]
        ], 'name');

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diperbaharui'
        ]);
    }

    //================================================================================================

    public function mainFormUpdate(): ResponseInterface
    {
        $permission = $this->checkPermission('websiteUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
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
