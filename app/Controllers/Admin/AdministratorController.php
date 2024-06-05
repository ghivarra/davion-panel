<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Ghivarra\DavionShield;
use App\Models\AdminModel;
use App\Models\AdminRoleModel;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;

class AdministratorController extends BaseController
{
    private function buildSearchQuery($orm, $columns)
    {
        foreach ($columns as $column):

            if ($column['key'] === 'status' OR $column['key'] === 'name')
            {
                $column['key'] = "admin.{$column['key']}";
            }

            if (strlen($column['query']) > 0)
            {
                if ($column['key'] === 'admin.status' OR $column['key'] === 'is_superadmin')
                {
                    $orm->where($column['key'], $column['query']);

                } elseif ($column['key'] === 'email_verified_at') {

                    $query = empty($column['query']) ? 'IS NULL' : 'IS NOT NULL';
                    $orm->where("{$column['key']} {$query}");

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
        $select  = ['admin.id', 'admin.name', 'username', 'is_superadmin', 'admin.status', 'email', 'email_verified_at', 'admin_role_id', 'photo'];

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

            $data[$key]['email_verified_at'] = is_null($item['email_verified_at']) ? 'Belum Verifikasi' : 'Terverifikasi';

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
}
