<?php namespace App\Controllers\Admin;

use App\Models\AdminModel;
use App\Models\AdminRoleModel;
use App\Controllers\BaseController;
use App\Libraries\Ghivarra\DavionShield;
use CodeIgniter\Database\RawSql;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AdministratorController extends BaseController
{
    private function mainAdminFreezed(): ResponseInterface
    {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Setelan akun admin utama tidak bisa dirubah. Buat admin baru untuk mencoba fitur-fitur pada Davion Panel',
        ]);
    }

    //================================================================================================

    private function buildSearchQuery($orm, $columns)
    {
        foreach ($columns as $column):

            if ($column['key'] === 'status' OR $column['key'] === 'name')
            {
                $column['key'] = "admin.{$column['key']}";

            } elseif ($column['key'] === 'admin_role_name') {

                $column['key'] = 'admin_role_id';
            }

            if (strlen($column['query']) > 0)
            {
                if ($column['key'] === 'admin.status' OR $column['key'] === 'admin_role_id')
                {
                    $orm->where($column['key'], $column['query']);

                } else {

                    if (str_contains($column['key'], '.'))
                    {
                        $orm->like($column['key'], $column['query'], 'both', null, true);
    
                    } else {
                        
                        $orm->like("admin.{$column['key']}", $column['query'], 'both', null, true);
                    }
                }
            }

        endforeach;

        return $orm;
    }

    //================================================================================================

    public function create(): ResponseInterface
    {
        $permission = $this->checkPermission('adminCreate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // get data
        $data = [
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('admin_email'),
            'name'         => $this->request->getPost('admin_fullname'),
            'role'         => $this->request->getPost('admin_role_id'),
            'password'     => $this->request->getPost('password'),
            'confirmation' => $this->request->getPost('confirmation_password'),
            'photo'        => $this->request->getFile('photo'),
        ];

        // validator
        $validator = Services::validation();
        $validator->setRules([
            'username'     => ['label' => 'Username', 'rules' => 'required|max_length[100]|alpha_dash|is_unique[admin.username]'],
            'email'        => ['label' => 'Email', 'rules' => 'required|max_length[100]|valid_email|is_unique[admin.email]'],
            'name'         => ['label' => 'Nama Lengkap', 'rules' => 'required|max_length[200]'],
            'role'         => ['label' => 'Role', 'rules' => 'required|is_not_unique[admin_role.id]'],
            'password'     => ['label' => 'Password', 'rules' => 'required|min_length[10]'],
            'confirmation' => ['label' => 'Konfirmasi Password', 'rules' => 'required|matches[password]'],
        ]);

        if (!$validator->run($data))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(', ', $validator->getErrors()),
            ]);
        }

        // check and validate if photo is uploaded
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

        // save
        $savedData = [
            'username'      => $data['username'],
            'email'         => $data['email'],
            'name'          => $data['name'],
            'admin_role_id' => $data['role'],
            'password'      => password_hash($data['password'], PASSWORD_DEFAULT),
            'photo'         => NULL
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

    public function datatable(): ResponseInterface
    {
        $permission = $this->checkPermission('adminView');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // create model instance
        $orm = new AdminModel();

        // get input
        $draw    = $this->request->getPost('draw');
        $all     = $this->request->getPost('all');
        $limit   = intval($this->request->getPost('limit'));
        $offset  = intval($this->request->getPost('offset'));
        $order   = $this->request->getPost('order');
        $columns = $this->request->getPost('columns');
        $select  = ['admin.id', 'admin.name', 'username', 'is_superadmin', 'admin.status', 'email', 'email_verified_at', 'admin_role_id', 'admin_role.name as admin_role_name', 'photo'];

        // set order column and dir
        $defaultOrderCol = 'name';
        $defaultOrderDir = 'ASC';
        $orderColumn     = isset($order['column']) ? $order['column'] : 'name';
        $orderDir        = isset($order['dir']) ? strtoupper($order['dir']) : 'ASC';

        // get total
        $total = $orm->countAllResults();

        // no query
        $orm = $orm->select($select)
                   ->join('admin_role', 'admin_role_id = admin_role.id', 'left')
                   ->orderBy($orderColumn, $orderDir)
                   ->orderBy($defaultOrderCol, $defaultOrderDir);
        
        if ($all !== 'true')
        {
            $orm = $orm->limit($limit, $offset);
        }

        // get filtered total
        $orm           = $this->buildSearchQuery($orm, $columns);
        $filteredTotal = $orm->countAllResults(false);
        
        if ($all !== 'true')
        {
            $orm = $orm->limit($limit, $offset);
        }

        // get data
        $orm  = $this->buildSearchQuery($orm, $columns);
        $data = $orm->find();

        // parse data
        foreach ($data as $key => $item):

            $data[$key]['email_verified_at'] = is_null($item['email_verified_at']) ? 'Belum Terverifikasi' : 'Terverifikasi';

        endforeach;

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => [
                'draw'            => $draw,
                'length'          => count($data),
                'recordsTotal'    => $total,
                'recordsFiltered' => $filteredTotal,
                'row'             => numbering($data, $offset)
            ]
        ]);
    }

    //================================================================================================

    public function delete(): ResponseInterface
    {
        $permission = $this->checkPermission('adminDelete');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id' => ['label' => 'Admin', 'rules' => 'required|numeric|is_not_unique[admin.id]']
        ];

        $data = $this->request->getPost(array_keys($rules));

        // Hard code to not change admin config
        if (intval($data['id']) === 1) 
        {
            return $this->mainAdminFreezed();
        }

        if (!$this->validateData($data, $rules))
        {
            // return
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data tidak tervalidasi',
                'data'    => $this->validator->getErrors()
            ]);
        }

        // delete data
        $orm = new AdminModel();

        // change unique alias, we use rawsql
        $updatedEmail    = new RawSql("CONCAT(admin.email, '--deleted-at-". time() ."')");
        $updatedUsername = new RawSql("CONCAT(admin.username, '--deleted-at-". time() ."')");

        // update
        $orm->set('email', $updatedEmail, false);
        $orm->set('username', $updatedUsername, false);
        $orm->where('id', $data['id'])->update();

        // delete
        $orm->delete($data['id']);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    //================================================================================================

    public function deleteSession(): ResponseInterface
    {
        $permission = $this->checkPermission('adminSession');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        $data = [
            'session_id' => $this->request->getPost('session_id'),
            'admin_id'   => $this->request->getPost('admin_id'),
        ];

        // Hard code to not change admin config
        if (intval($data['admin_id']) === 1) 
        {
            return $this->mainAdminFreezed();
        }

        // get session based on id
        $auth     = new DavionShield();
        $sessions = $auth->getSessionFromUser($data['admin_id']);
        $sessions = empty($sessions) ? [] : array_column($sessions, 'id');

        if (empty($sessions))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Sesi Login tidak valid/tidak ditemukan',
            ]);
        }

        // validator
        $validator = Services::validation();
        $validator->setRules([
            'session_id' => ['label' => 'Sesi Login', 'rules' => 'required|in_list['. implode(',', $sessions) .']'],
            'admin_id'   => ['label' => 'Akun Admin', 'rules' => 'required|is_not_unique[admin.id]'],
        ]);

        // check
        if (!$validator->run($data))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(', ', $validator->getErrors()),
            ]);
        }

        // delete
        $deleteSession = $auth->deleteSession($data['session_id']);

        // return
        if (!$deleteSession)
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus sesi login, silahkan coba lagi',
            ]);

        } else {

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Sesi berhasil dihapus',
            ]);
        }
    }

    //================================================================================================

    public function get(): ResponseInterface
    {
        $permission = $this->checkPermission('adminView');

        if (!$permission)
        {
            return cannotAccessModule();
        }

        // create ORM instance
        $orm  = new AdminModel();
        $data = $orm->select(['admin.id', 'admin.name', 'username', 'is_superadmin', 'admin.status', 'email', 'email_verified_at', 'admin_role_id', 'admin_role.name as admin_role_name', 'photo'])
                    ->join('admin_role', 'admin_role_id = admin_role.id', 'left')
                    ->where('admin.id', $this->request->getGet('id'))
                    ->first();

        if (empty($data) OR !$data)
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan',
                'data'    => NULL
            ]);
        }

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $data
        ]);
    }

    //================================================================================================

    public function getRoleList(): ResponseInterface
    {
        $createPermission = $this->checkPermission('adminCreate');
        $updatePermission = $this->checkPermission('adminUpdate');
        
        if (!$createPermission && !$updatePermission)
        {
            return cannotAccessModule();
        }

        $orm = new AdminRoleModel();

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $orm->select(['id', 'name'])->find()
        ]);
    }

    //================================================================================================

    public function getSession(): ResponseInterface
    {
        $permission = $this->checkPermission('adminSession');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validasi
        $adminId = $this->request->getGet('id');

        // validator
        $validator = Services::validation();
        $validator->setRules([
            'id' => ['label' => 'Admin', 'rules' => 'required|is_not_unique[admin.id]']
        ]);

        if (!$validator->run(['id' => $adminId]))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(', ', $validator->getErrors()),
            ]);
        }

        // get session from id
        $auth     = new DavionShield();
        $sessions = $auth->getSessionFromUser($adminId);

        // delete session name for security issue
        foreach ($sessions as $n => $session):

            unset($sessions[$n]['name']);

        endforeach;
        
        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $sessions
        ]);
    }

    //================================================================================================

    public function update(): ResponseInterface
    {
        $permission = $this->checkPermission('adminUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // get data
        $data = [
            'id'           => $this->request->getPost('id'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('admin_email'),
            'name'         => $this->request->getPost('admin_fullname'),
            'role'         => $this->request->getPost('admin_role_id'),
            'password'     => $this->request->getPost('password'),
            'confirmation' => $this->request->getPost('confirmation_password'),
            'photo'        => $this->request->getFile('photo'),
        ];

        // Hard code to not change admin config
        if (intval($data['id']) === 1) 
        {
            return $this->mainAdminFreezed();
        }

        // rules
        $rules = [
            'id'   => ['label' => 'Akun', 'rules' => 'required|is_not_unique[admin.id]'],
            'name' => ['label' => 'Nama Lengkap', 'rules' => 'required|max_length[200]'],
            'role' => ['label' => 'Role', 'rules' => 'required|is_not_unique[admin_role.id]'],
        ];

        // check if account exist and get data
        $admin   = new AdminModel();
        $oldData = $admin->select(['id', 'username', 'email'])->find($data['id']);

        if (empty($oldData))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Akun Tidak Ditemukan',
            ]);
        }

        // if username changed
        if ($data['username'] !== $oldData['username'])
        {
            $rules['username'] = ['label' => 'Username', 'rules' => 'required|max_length[100]|alpha_dash|is_unique[admin.username]'];
        }

        // if email changed
        if ($data['email'] !== $oldData['email'])
        {
            $rules['email'] = ['label' => 'Email', 'rules' => 'required|max_length[100]|valid_email|is_unique[admin.email]'];
        }

        // check if password is changed
        if (!empty($data['password']))
        {
            $rules['password']     = ['label' => 'Password', 'rules' => 'required|min_length[10]'];
            $rules['confirmation'] = ['label' => 'Konfirmasi Password', 'rules' => 'required|matches[password]'];
        }

        // validator
        $validator = Services::validation();
        $validator->setRules($rules);
        
        if (!$validator->run($data))
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(', ', $validator->getErrors()),
            ]);
        }

        // check and validate if photo is uploaded
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

        // save
        $savedData = [
            'id'            => $data['id'],
            'username'      => $data['username'],
            'email'         => $data['email'],
            'name'          => $data['name'],
            'admin_role_id' => $data['role'],
        ];

        // if password changed
        if (!empty($data['password']))
        {
            $savedData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // if uploaded
        if ($data['photo']->isValid())
        {
            $savedData['photo'] = $data['photo']->getRandomName();
            $data['photo']->move(WRITEPATH . 'app/images/admin', $savedData['photo']);
        }

        // save
        $admin->save($savedData);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "Akun {$savedData['name']} berhasil diperbaharui",
        ]);
    }

    //================================================================================================

    public function updateStatus(): ResponseInterface
    {
        $permission = $this->checkPermission('adminUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id'     => ['label' => 'Admin', 'rules' => 'required|numeric|is_not_unique[admin.id]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
        ];

        $data = $this->request->getPost(array_keys($rules));

        // Hard code to not change admin config
        if (intval($data['id']) === 1) 
        {
            return $this->mainAdminFreezed();
        }

        if (!$this->validateData($data, $rules))
        {
            // return
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data tidak tervalidasi',
                'data'    => $this->validator->getErrors()
            ]);
        }

        // save data
        $orm = new AdminModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Status data berhasil diperbaharui'
        ]);
    }

    //================================================================================================
}
