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
}