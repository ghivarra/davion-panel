<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Ghivarra\DavionShield;
use App\Models\AdminModel;

class AccountController extends BaseController
{
    public function changePassword(): ResponseInterface
    {
        $auth    = new DavionShield();
        $account = $auth->getAccountData();

        // validate password
        $rules = [
            'old_password'  => ['label' => 'Password Lama', 'rules' => 'required'],
            'new_password'  => ['label' => 'Password Baru', 'rules' => 'required|min_length[10]'],
            'conf_password' => ['label' => 'Konfirmasi Password Baru', 'rules' => 'required|matches[new_password]'],
        ];

        $data      = $this->request->getPost(array_keys($rules));
        $validator = Services::validation();
        $validator->setRules($rules);

        if (!$validator->run($data))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(', ', $validator->getErrors()),
            ]);
        }

        // check if old password matched
        $orm         = new AdminModel();
        $accountData = $orm->select(['id', 'password'])->find($account['id']);

        if (!password_verify($data['old_password'], $accountData['password']))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Password lama tidak sesuai',
            ]);
        }

        // update password
        $orm->save([
            'id'       => $account['id'],
            'password' => password_hash($data['new_password'], PASSWORD_DEFAULT)
        ]);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Password akun anda berhasil diperbaharui',
        ]);
    }

    //================================================================================================

    public function update(): ResponseInterface
    {
        $auth    = new DavionShield();
        $account = $auth->getAccountData();

        // validate data
        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'photo' => $this->request->getFile('photo')
        ];

        $rules = [
            'name'  => ['label' => 'Nama Lengkap', 'rules' => 'required|max_length[100]'],
            'email' => ['label' => 'Email', 'rules' => 'required|valid_email'],
        ];

        // check if email is changed
        if ($account['email'] !== $data['email'])
        {
            $rules['email'] = ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[admin.email]'];
        }

        // run validation
        $validator = Services::validation();
        $validator->setRules($rules);

        // check
        if (!$validator->run($data))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(', ', $validator->getErrors()),
            ]);
        }

        // check if photo is changed
        if ($data['photo']->isValid())
        {
            $validImage = $this->validateData([], [
                'photo' => ['label' => 'Foto', 'rules' => 'uploaded[photo]|max_size[photo,4096]|is_image[photo]|mime_in[photo,image/png,image/gif,image/jpeg]']
            ]);

            if (!$validImage)
            {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => implode(', ', $validator->getErrors()),
                ]);
            }
        }

        // lanjut save
        $savedData = [
            'id'    => $account['id'],
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        // if uploaded
        if ($data['photo']->isValid())
        {
            $savedData['photo'] = $data['photo']->getRandomName();
            $data['photo']->move(WRITEPATH . 'app/images/admin', $savedData['photo']);
        }

        // save
        $admin = new AdminModel();
        $admin->save($savedData);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Akun anda berhasil diperbaharui',
        ]);
    }

    //================================================================================================
}