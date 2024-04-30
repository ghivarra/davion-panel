<?php

namespace App\Controllers\Admin;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\AdminMenuGroupModel;
use App\Models\AdminMenuModel;
use Config\Services;

class MenuController extends BaseController
{
    public function list(): ResponseInterface
    {
        $permission = $this->checkPermission('menuView');
        
        if (!$permission)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
        }

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
}
