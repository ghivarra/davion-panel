<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AdminModuleModel;
use App\Models\AdminMenuModel;
use App\Models\AdminRoleModel;
use App\Models\AdminRoleMenuModel;
use App\Models\AdminRoleModuleModel;

class RoleController extends BaseController
{
    private function buildSearchQuery($orm, $columns)
    {
        foreach ($columns as $column):

            if (strlen($column['query']) > 0)
            {
                if ($column['key'] === 'status' OR $column['key'] === 'is_superadmin')
                {
                    $orm->where($column['key'], $column['query']);

                } else {

                    if (str_contains($column['key'], '.'))
                    {
                        $orm->like($column['key'], $column['query'], 'both', null, true);
    
                    } else {
                        
                        $orm->like("admin_role.{$column['key']}", $column['query'], 'both', null, true);
                    }
                }
            }

        endforeach;

        return $orm;
    }

    //================================================================================================

    public function allMenuList(): ResponseInterface
    {
        $permissionCreate = $this->checkPermission('roleCreate');
        $permissionUpdate = $this->checkPermission('roleUpdate');
        
        if (!$permissionCreate OR !$permissionUpdate)
        {
            return cannotAccessModule();
        }

        $orm = new AdminMenuModel();
        $all = $orm->select(['admin_menu.id', 'admin_menu.title', 'type', 'admin_menu_group.name as group_name', 'admin_menu_parent_id'])
                   ->join('admin_menu_group', 'admin_menu_group_id = admin_menu_group.id', 'left')
                   ->orderBy('admin_menu.title', 'ASC')
                   ->find();

        // parse data
        $groups =  array_unique(array_column($all, 'group_name'));
        sort($groups);

        // search child menu
        foreach ($all as $key => $item)
        {
            // add new key
            $all[$key]['checked'] = false;

            // search child
            if ($item['type'] === 'Child')
            {
                // search parent
                foreach ($all as $n => $menu)
                {
                    if ($menu['id'] === $item['admin_menu_parent_id'])
                    {
                        if (!isset($all[$n]['childs']))
                        {
                            $all[$n]['childs'] = [];
                        }

                        array_push($all[$n]['childs'], $all[$key]);
                    }
                }

                // unset
                unset($all[$key]);

            } else {

                if (!isset($all[$key]['childs']))
                {
                    $all[$key]['childs'] = [];
                }

            }
        }

        // input parsed menu to groups
        $result = [];

        foreach ($groups as $key => $name):

            $result[$key]['name']  = $name;
            $result[$key]['menus'] = [];

            // search primary and parent menu
            foreach ($all as $n => $item)
            {
                if ($item['group_name'] === $name && ($item['type'] === 'Primary' OR $item['type'] === 'Parent'))
                {
                    array_push($result[$key]['menus'], $item);
                    unset($all[$n]);
                }
            }

        endforeach;

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $result
        ]);
    }

    //================================================================================================

    public function allModuleList(): ResponseInterface
    {
        $permissionCreate = $this->checkPermission('roleCreate');
        $permissionUpdate = $this->checkPermission('roleUpdate');
        
        if (!$permissionCreate OR !$permissionUpdate)
        {
            return cannotAccessModule();
        }

        $orm = new AdminModuleModel();
        $all = $orm->select(['id', 'name', 'group'])
                   ->orderBy('name', 'ASC')
                   ->find();

        // parse data
        $groups =  array_unique(array_column($all, 'group'));
        sort($groups);

        // 
        $result = [];

        foreach ($groups as $key => $name):

            $result[$key]['name']    = $name;
            $result[$key]['checked'] = false;
            $result[$key]['modules'] = [];

            // search
            foreach ($all as $n => $item)
            {
                $item['checked'] = false;

                if ($item['group'] === $name)
                {
                    array_push($result[$key]['modules'], $item);
                    unset($all[$n]);
                }
            }

        endforeach;

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $result
        ]);
    }

    //================================================================================================

    public function create(): ResponseInterface
    {
        $permission = $this->checkPermission('roleCreate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // data
        $data = [
            'name'          => $this->request->getPost('name'),
            'is_superadmin' => $this->request->getPost('is_superadmin')
        ];

        // set rules
        $rules = [
            'name'          => ['label' => 'Nama Role', 'rules' => 'required|max_length[150]'],
            'is_superadmin' => ['label' => 'Tipe Role', 'rules' => 'required|in_list[0,1]'],
        ];

        // if not superadmin, update rules and modules
        if ($data['is_superadmin'] === '0')
        {
            $data['modules'] = json_decode($this->request->getPost('modules'), true);
            $data['menus']   = json_decode($this->request->getPost('menus'), true);

            // get all modules & menu so we not n+1           
            if (!empty($data['modules']))
            {
                $adminModule = new AdminModuleModel();
                $allModules  = implode(',', array_column($adminModule->select('id')->find(), 'id'));
                
                $rules['modules.*'] = ['label' => 'Pilihan Modul', 'rules' => 'in_list['. $allModules .']'];
            }

            if (!empty($data['menus']))
            {
                $adminMenu = new AdminMenuModel();
                $allMenus  = implode(',', array_column($adminMenu->select('id')->find(), 'id'));

                $rules['menus.*'] = ['label' => 'Pilihan Menu', 'rules' => 'in_list['. $allMenus .']'];
            }
        }

        // validator
        $validator = Services::validation();
        $validator->setRules($rules);

        if (!$validator->run($data))
        {
            // return
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data tidak tervalidasi',
                'data'    => implode(', ', $validator->getErrors())
            ]);
        }
        
        // if valid
        $db = \Config\Database::connect();

        // transaction start
        $db->transStart();

        $adminRole = new AdminRoleModel();
        $adminRole->save([
            'name'          => $data['name'],
            'is_superadmin' => intval($data['is_superadmin']),
            'status'        => 'Aktif'
        ]);

        // get last id
        $roleId = $adminRole->insertID();

        // insert menus
        if (!empty($data['menus']))
        {
            $menus = [];

            foreach ($data['menus'] as $menu):

                array_push($menus, [
                    'admin_role_id' => $roleId,
                    'admin_menu_id' => $menu
                ]);

            endforeach;

            // save
            $adminRoleMenu = new AdminRoleMenuModel();
            $adminRoleMenu->insertBatch($menus);
        }

        // insert modules
        if (!empty($data['modules']))
        {
            $modules = [];

            foreach ($data['modules'] as $modul):

                array_push($modules, [
                    'admin_role_id'   => $roleId,
                    'admin_module_id' => $modul
                ]);

            endforeach;

            // save
            $adminRoleModule = new AdminRoleModuleModel();
            $adminRoleModule->insertBatch($modules);
        }

        // check transaction status
        if ($db->transStatus() === false)
        {
            // rollback
            $db->transRollback();

            // early return
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data tidak tervalidasi',
                'data'    => 'Gagal menginput data, ada error di database'
            ]);
        }

        $db->transCommit();

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Role berhasil diinput',
        ]);
    }

    //================================================================================================

    public function datatable(): ResponseInterface
    {
        $permission = $this->checkPermission('roleView');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // create model instance
        $orm = new AdminRoleModel();

        // get input
        $draw    = $this->request->getPost('draw');
        $all     = $this->request->getPost('all');
        $limit   = intval($this->request->getPost('limit'));
        $offset  = intval($this->request->getPost('offset'));
        $order   = $this->request->getPost('order');
        $columns = $this->request->getPost('columns');
        $select  = ['id', 'name', 'is_superadmin', 'status'];

        // set order column and dir
        $defaultOrderCol = 'name';
        $defaultOrderDir = 'ASC';
        $orderColumn     = isset($order['column']) ? $order['column'] : 'name';
        $orderDir        = isset($order['dir']) ? strtoupper($order['dir']) : 'ASC';

        // get total
        $total = $orm->countAllResults();

        // no query
        $orm = $orm->select($select)
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
        $permission = $this->checkPermission('roleDelete');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id' => ['label' => 'Role', 'rules' => 'required|numeric|is_not_unique[admin_role.id]']
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
        $orm = new AdminRoleModel();
        $orm->delete($data['id']);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    //================================================================================================

    public function updateStatus(): ResponseInterface
    {
        $permission = $this->checkPermission('roleUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id'     => ['label' => 'Role', 'rules' => 'required|numeric|is_not_unique[admin_role.id]'],
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
        $orm = new AdminRoleModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Status data berhasil diperbaharui'
        ]);
    }

    //================================================================================================
}