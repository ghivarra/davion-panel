<?php

namespace App\Controllers\Admin;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\AdminMenuGroupModel;
use App\Models\AdminMenuModel;
use Config\Services;

class MenuController extends BaseController
{
    public function create(): ResponseInterface
    {
        $permission = $this->checkPermission('menuCreate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        $type  = $this->request->getPost('type');
        $rules = [
            'type'                => ['label' => 'Tipe', 'rules' => 'required|in_list[Primary,Parent,Child]'],
            'title'               => ['label' => 'Label', 'rules' => 'required|max_length[200]'],
            'status'              => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
            'admin_menu_group_id' => ['label' => 'Grup', 'rules' => 'required|is_not_unique[admin_menu_group.id]'],
        ];

        if ($type !== 'Child')
        {
            $rules['icon'] = ['label' => 'Icon', 'rules' => 'required|max_length[100]'];
        }

        if ($type !== 'Parent')
        {
            $rules['router_name'] = ['label' => 'Nama Router', 'rules' => 'required|max_length[200]'];
        }

        // create data and validate
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

        // save
        $orm = new AdminMenuModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Menu berhasil ditambah'
        ]);
    }

    //================================================================================================

    public function delete(): ResponseInterface
    {
        $permission = $this->checkPermission('menuDelete');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id' => ['label' => 'Menu', 'rules' => 'required|numeric|is_not_unique[admin_menu.id]']
        ];

        $data = $this->request->getPost(array_keys($rules));

        // HARD CODE SO DEFAULT NOT GET DELETED, NONACTIVED, OR CHANGED
        if (intval($data['id']) === 1)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
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

        // save
        $orm = new AdminMenuModel();
        $orm->delete($data['id']);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Menu berhasil dihapus'
        ]);
    }

    //================================================================================================

    public function get(): ResponseInterface
    {
        $permission = $this->checkPermission('menuView');

        if (!$permission)
        {
            return cannotAccessModule();
        }

        // session not needed anymore, unlock the session file mechanism
        session_write_close();

        // create ORM instance
        $orm  = new AdminMenuModel();
        $data = $orm->select(['admin_menu.id', 'admin_menu.status', 'title', 'router_name', 'icon', 'type', 'admin_menu_parent_id', 'admin_menu_group_id', 'name as admin_menu_group_name'])
                    ->join('admin_menu_group', 'admin_menu_group_id = admin_menu_group.id', 'left')
                    ->where('admin_menu.id', $this->request->getGet('id'))
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

    public function groupCreate(): ResponseInterface
    {
        $permission = $this->checkPermission('menuCreate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'name'   => ['label' => 'Nama Grup Menu', 'rules' => 'required|max_length[200]|not_in_list[Default]'],
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

        // save
        $orm = new AdminMenuGroupModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Grup menu berhasil ditambah'
        ]);
    }

    //================================================================================================

    public function groupDelete(): ResponseInterface
    {
        $permission = $this->checkPermission('menuDelete');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id' => ['label' => 'Grup Menu', 'rules' => 'required|numeric|is_not_unique[admin_menu_group.id]']
        ];

        $data = $this->request->getPost(array_keys($rules));

        // HARD CODE SO DEFAULT NOT GET DELETED, NONACTIVED, OR CHANGED
        if (intval($data['id']) === 1)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
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

        // save
        $orm = new AdminMenuGroupModel();
        $orm->delete($data['id']);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Grup menu berhasil dihapus'
        ]);
    }

    //================================================================================================

    public function groupUpdate(): ResponseInterface
    {
        $permission = $this->checkPermission('menuUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id'     => ['label' => 'Grup Menu', 'rules' => 'required|numeric|is_not_unique[admin_menu_group.id]'],
            'name'   => ['label' => 'Nama Grup Menu', 'rules' => 'required|max_length[200]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
        ];

        $data = $this->request->getPost(array_keys($rules));

        // HARD CODE SO DEFAULT NOT GET DELETED, NONACTIVED, OR CHANGED
        if (intval($data['id']) === 1)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
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

        // save
        $orm = new AdminMenuGroupModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Grup menu berhasil diperbaharui'
        ]);
    }

    //================================================================================================

    public function groupUpdateStatus(): ResponseInterface
    {
        $permission = $this->checkPermission('menuUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id'     => ['label' => 'Grup Menu', 'rules' => 'required|numeric|is_not_unique[admin_menu_group.id]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
        ];

        $data = $this->request->getPost(array_keys($rules));

        // HARD CODE SO DEFAULT NOT GET DELETED, NONACTIVED, OR CHANGED
        if (intval($data['id']) === 1)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
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

        // save
        $orm = new AdminMenuGroupModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Status data berhasil diperbaharui'
        ]);
    }

    //================================================================================================

    public function list(): ResponseInterface
    {
        $permission = $this->checkPermission('menuView');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // session not needed anymore, unlock the session file mechanism
        session_write_close();

        // get menu groups
        $adminMenuGroup = new AdminMenuGroupModel();
        $adminMenu      = new AdminMenuModel();
        $menuGroup      = $adminMenuGroup->select(['id', 'name', 'status'])->orderBy('sort_order', 'ASC')->find();

        // get all menu
        foreach ($menuGroup as $n => $item):

            $menus = $adminMenu->select(['id', 'title', 'icon', 'type', 'status'])
                               ->where('admin_menu_group_id', $item['id'])
                               ->where('type !=', 'Child')
                               ->orderBy('sort_order', 'ASC')
                               ->find();

            if (empty($menus))
            {
                // set empty child
                $menuGroup[$n]['child'] = [];
                continue;
            }

            foreach ($menus as $i => $menu)
            {
                if ($menu['type'] === 'Parent')
                {
                    $childs = $adminMenu->select(['id', 'title', 'icon', 'status'])
                                        ->where('admin_menu_parent_id', $menu['id'])
                                        ->orderBy('sort_order', 'ASC')
                                        ->find();

                    if (empty($childs))
                    {
                        $menus[$i]['child'] = [];

                    } else {

                        foreach ($childs as $x => $child):

                            $childs[$x]['child'] = [];

                        endforeach;

                        $menus[$i]['child'] = $childs;
                    }

                } else {

                    $menus[$i]['child'] = [];
                }
            }

            // set child
            $menuGroup[$n]['child'] = $menus;

        endforeach;

        

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $menuGroup
        ]);
    }

    //================================================================================================

    public function sort(): ResponseInterface
    {
        $permission = $this->checkPermission('menuUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        $data = [
            'group' => $this->request->getPost('group'),
            'menu'  => $this->request->getPost('menu'),
        ];

        // initiate db to initiate transaction and update batch
        $db = \Config\Database::connect();
        $db->transStart();

        // update group
        foreach ($data['group'] as $n => $item):

            $setGroup[$n] = [
                'id'         => intval($item['id']),
                'sort_order' => intval($item['sort_order']),
                'updated_at' => date('Y-m-d H:i:s')
            ];

        endforeach;

        $db->table('admin_menu_group')->updateBatch($setGroup, 'id');

        // update menu
        foreach ($data['menu'] as $n => $item):

            $setMenu[$n] = [
                'id'                   => intval($item['id']),
                'type'                 => $item['type'],
                'sort_order'           => intval($item['sort_order']),
                'admin_menu_group_id'  => isset($item['admin_menu_group_id']) ? $item['admin_menu_group_id'] : null,
                'admin_menu_parent_id' => isset($item['admin_menu_parent_id']) ? $item['admin_menu_parent_id'] : null,
                'updated_at'           => date('Y-m-d H:i:s')
            ];

        endforeach;

        $db->table('admin_menu')->updateBatch($setMenu, 'id');
        
        // check if transaction failed
        if ($db->transStatus() === false)
        {
            $db->transRollback();
            return $this->response->setStatusCode(503)->setJSON([
                'status'  => 'error',
                'message' => 'Server sedang sibuk'
            ]);
        }

        // commit transaction
        $db->transCommit();

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Susunan menu berhasil diperbaharui'
        ]);
    }

    //================================================================================================

    public function update(): ResponseInterface
    {
        $permission = $this->checkPermission('menuUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        $type  = $this->request->getPost('type');
        $rules = [
            'id'                  => ['label' => 'Menu', 'rules' => 'required|is_not_unique[admin_menu.id]'],
            'title'               => ['label' => 'Label', 'rules' => 'required|max_length[200]'],
            'status'              => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
        ];

        if ($type !== 'Child')
        {
            $rules['icon'] = ['label' => 'Icon', 'rules' => 'required|max_length[100]'];
        }

        if ($type !== 'Parent')
        {
            $rules['router_name'] = ['label' => 'Nama Router', 'rules' => 'required|max_length[200]'];
        }

        // create data and validate
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

        // save
        $orm = new AdminMenuModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Menu berhasil diperbaharui'
        ]);
    }

    //================================================================================================

    public function updateStatus(): ResponseInterface
    {
        $permission = $this->checkPermission('menuUpdate');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // validate data
        $rules = [
            'id'     => ['label' => 'Menu', 'rules' => 'required|numeric|is_not_unique[admin_menu.id]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[Aktif,Nonaktif]'],
        ];

        $data = $this->request->getPost(array_keys($rules));

        // HARD CODE SO DEFAULT NOT GET DELETED, NONACTIVED, OR CHANGED
        if (intval($data['id']) === 1)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
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

        // save
        $orm = new AdminMenuModel();
        $orm->save($data);

        // return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Status data berhasil diperbaharui'
        ]);
    }

    //================================================================================================ 
}
