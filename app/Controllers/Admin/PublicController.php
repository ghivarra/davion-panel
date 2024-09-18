<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Ghivarra\DavionShield;
use App\Models\WebsiteModel;
use App\Models\AdminMenuModel;
use App\Models\AdminRoleMenuModel;
use CodeIgniter\HTTP\ResponseInterface;

class PublicController extends BaseController
{
    public function logout(): ResponseInterface
    {
        $davionShield = new DavionShield();
        $davionShield->logout();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Anda berhasil keluar/logout dari panel',
        ]);
    }

    //================================================================================================

    public function menu(): ResponseInterface
    {
        $davionShield = new DavionShield();
        $accountData  = $davionShield->getAccountData();
        
        // check if superadmin
        if ($accountData['is_superadmin'] === SUPERADMIN)
        {
            $orm       = new AdminMenuModel();
            $adminMenu = $orm->getSuperadminMenu();

        } else {

            $orm       = new AdminRoleMenuModel();
            $adminMenu = $orm->getRoleMenu($accountData['admin_role_id']);
        }

        // add isactive
        foreach ($adminMenu as $key => $group):

            if (isset($group['menu']) && !empty($group['menu']))
            {
                foreach ($group['menu'] as $menuKey => $menu):

                    $adminMenu[$key]['menu'][$menuKey]['is_active'] = false;

                    if (isset($menu['childs']) && !empty($menu['childs']))
                    {
                        foreach ($menu['childs'] as $childKey => $child)
                        {
                            $adminMenu[$key]['menu'][$menuKey]['childs'][$childKey]['is_active'] = false;
                        }
                    }

                endforeach;
            }

        endforeach;

        /// return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $adminMenu
        ]);
    }

    //================================================================================================

    public function searchMenu(): ResponseInterface
    {
        $davionShield = new DavionShield();
        $accountData  = $davionShield->getAccountData();
        $query        = $this->request->getPost('query');

        if (empty($query) OR strlen($query) < 2)
        {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data tidak ditemukan',
                'data'    => []
            ]);
        }

        // check if superadmin
        if ($accountData['is_superadmin'] === SUPERADMIN)
        {
            $adminMenuModel = new AdminMenuModel();
            $adminMenu      = $adminMenuModel->select(['admin_menu.id', 'title', 'router_name', 'icon', 'type', 'admin_menu_parent_id'])
                                             ->where('admin_menu.type !=', 'Parent')
                                             ->where('admin_menu.status', 'Aktif')
                                             ->like('title', $query, 'both', null, true)
                                             ->orderBy('title', 'ASC')
                                             ->find();

            // get all parents
            $adminMenuParent = $adminMenuModel->select(['id', 'icon', 'title'])
                                              ->where('type', 'Parent')
                                              ->find();

            $adminMenuParentId = array_column($adminMenuParent, 'id');

            foreach ($adminMenu as $n => $item):

                if ($item['type'] === 'Child')
                {
                    $key = array_keys($adminMenuParentId, $item['admin_menu_parent_id']);
                    $key = $key[0];

                    // fill data
                    $adminMenu[$n]['icon']  = $adminMenuParent[$key]['icon'];
                    $adminMenu[$n]['title'] = "{$adminMenuParent[$key]['title']} - {$item['title']}";
                }

            endforeach;

        } else {

            $adminMenuModel     = new AdminMenuModel();
            $adminRoleMenuModel = new AdminRoleMenuModel();
            $adminMenu          = $adminRoleMenuModel->select(['admin_menu_id as id', 'title', 'router_name', 'icon', 'type', 'admin_menu_parent_id'])
                                                     ->join('admin_menu', 'admin_menu_id = admin_menu.id', 'left')
                                                     ->where('admin_role_id', $accountData['admin_role_id'])
                                                     ->where('admin_menu.type !=', 'Parent')
                                                     ->where('admin_menu.status', 'Aktif')
                                                     ->like('title', $query, 'both', null, true)
                                                     ->orderBy('title', 'ASC')
                                                     ->find();

            // get all parents
            $adminMenuParent = $adminMenuModel->select(['id', 'icon', 'title'])
                                              ->where('type', 'Parent')
                                              ->find();

            $adminMenuParentId = array_column($adminMenuParent, 'id');

            foreach ($adminMenu as $n => $item):

                if ($item['type'] === 'Child')
                {
                    $key = array_keys($adminMenuParentId, $item['admin_menu_parent_id']);
                    $key = $key[0];

                    // fill data
                    $adminMenu[$n]['icon']  = $adminMenuParent[$key]['icon'];
                    $adminMenu[$n]['title'] = "{$adminMenuParent[$key]['title']} - {$item['title']}";
                }

            endforeach;
        }

        /// return
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditarik',
            'data'    => $adminMenu
        ]);
    }

    //================================================================================================

    public function sessionData(): ResponseInterface
    {
        $davionShield = new DavionShield();
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diambil',
            'data'    => $davionShield->getAccountData()
        ]);
    }

    //================================================================================================

    public function singlePageApplication(): ResponseInterface | string
    {
        if ($this->request->isAjax())
        {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Halaman tidak ditemukan',
            ])->setStatusCode(404);
        }

        $websiteModel = new WebsiteModel();

        // return and render vue
        return $this->vue->render('Panel/PanelIndexView', [
            'title'   => $_ENV['VITE_APP_NAME'],
            'website' => $websiteModel->getAllData()
        ]);
    }

    //================================================================================================
}
