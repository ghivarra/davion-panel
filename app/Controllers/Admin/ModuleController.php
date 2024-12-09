<?php

namespace App\Controllers\Admin;

use App\Models\AdminModuleModel;
use App\Controllers\BaseController;
use App\Libraries\Ghivarra\Datatable;
use CodeIgniter\Database\RawSql;
use CodeIgniter\HTTP\ResponseInterface;

class ModuleController extends BaseController
{
    public function create(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleCreate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'group'  => ['label' => 'Grup', 'rules' => 'required|max_length[100]'],
            'alias'  => ['label' => 'Alias', 'rules' => 'required|max_length[200]|is_unique[admin_module.alias]'],
            'name'   => ['label' => 'Nama', 'rules' => 'required|max_length[200]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (!$this->validateData($data, $rules))
        {
            // return
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data tidak tervalidasi',
                'data'    => $this->validator->getErrors()
            ]);
        }

        // create model instance
        $orm = new AdminModuleModel();

        // insert data
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diinput'
        ]);
    }

    //================================================================================================

    public function datatable(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleView');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // session not needed anymore, unlock the session file mechanism
        session_write_close();

        // set order column and dir
        $order           = $this->request->getPost('order');
        $defaultOrderCol = 'name';
        $defaultOrderDir = 'ASC';
        $orderColumn     = isset($order['column']) ? $order['column'] : 'name';
        $orderDir        = isset($order['dir']) ? strtoupper($order['dir']) : 'ASC';

        // datatable
        $datatable = new Datatable();

        // get datatable data
        $data = $datatable->fetch([
            'tableName'       => 'admin_module',
            'orm'             => new AdminModuleModel(),
            'selectedColumns' => [
                'id', 'alias', 'name', 'group', 'status'
            ],
            'getAllData'  => ($this->request->getPost('all') === 'true') ? true : false,
            'limit'       => intval($this->request->getPost('limit')),
            'offset'      => intval($this->request->getPost('offset')),
            'drawCount'   => intval($this->request->getPost('draw')),
            'columnQuery' => $this->request->getPost('columns'),
            'orders'      => [
                ['column' => $orderColumn, 'order' => $orderDir],
                ['column' => $defaultOrderCol, 'order' => $defaultOrderDir],
            ],
            
            // parameters
            'joinParams' => [
                // not used
            ],
            'defaultParams' => [
                // not used
            ],
            'searchParams' => [
                'status' => [
                    'type'      => 'is',
                    'targetKey' => 'admin_module.status',
                ],
            ]
        ]);

        // return
        return $this->response->setJSON($data);
    }

    //================================================================================================

    public function delete(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleDelete');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id' => ['label' => 'Modul', 'rules' => 'required|numeric|is_not_unique[admin_module.id]']
        ];

        $data = $this->request->getPost(array_keys($rules));

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
        $orm = new AdminModuleModel();

        // change unique alias, we use rawsql
        $updatedAlias = new RawSql("CONCAT(admin_module.alias, '--deleted-at-". time() ."')");

        $orm->set('alias', $updatedAlias, false);
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

    public function get(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleView');

        if (!$permission)
        {
            return cannotAccessModule();
        }

        // session not needed anymore, unlock the session file mechanism
        session_write_close();

        // create ORM instance
        $orm  = new AdminModuleModel();
        $data = $orm->where('alias', $this->request->getGet('alias'))->first();

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

    public function groupList(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleView');

        if (!$permission)
        {
            return cannotAccessModule();
        }

        // session not needed anymore, unlock the session file mechanism
        session_write_close();

        // create ORM instance
        $orm  = new AdminModuleModel();
        $data = $orm->select('group')->distinct()->find();

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => array_column($data, 'group')
        ]);
    }

    //================================================================================================

    public function update(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // create model instance
        $orm = new AdminModuleModel();

        // check
        $check = $orm->where('id !=', $this->request->getPost('id'))
                     ->where('alias', $this->request->getPost('alias'))
                     ->countAllResults();

        if (!empty($check)) 
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Alias modul harus unik dan tidak sama dengan modul lain'
            ]);
        }

        // validate data
        $rules = [
            'id'     => ['label' => 'Modul', 'rules' => 'required|numeric|is_not_unique[admin_module.id]'],
            'group'  => ['label' => 'Grup', 'rules' => 'required|max_length[100]'],
            'alias'  => ['label' => 'Alias', 'rules' => 'required|max_length[200]'],
            'name'   => ['label' => 'Nama', 'rules' => 'required|max_length[200]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (!$this->validateData($data, $rules))
        {
            // return
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data tidak tervalidasi',
                'data'    => $this->validator->getErrors()
            ]);
        }

        // insert data
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diperbaharui'
        ]);
    }

    //================================================================================================

    public function updateStatus(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id'     => ['label' => 'Modul', 'rules' => 'required|numeric|is_not_unique[admin_module.id]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
        ];

        $data = $this->request->getPost(array_keys($rules));

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
        $orm = new AdminModuleModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Status data berhasil diperbaharui'
        ]);
    }

    //================================================================================================
}
