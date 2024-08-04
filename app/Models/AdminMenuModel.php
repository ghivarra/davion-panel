<?php

namespace App\Models;

use App\Models\AdminMenuGroupModel;
use CodeIgniter\Model;

class AdminMenuModel extends Model
{
    protected $table            = 'admin_menu';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 'router_name', 'icon', 'sort_order', 'status', 'type', 'admin_menu_parent_id', 'admin_menu_group_id'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    //================================================================================================

    public function getSuperadminMenu(): array
    {
        $primaryParentMenu = $this->select(['admin_menu.id', 'title', 'router_name', 'icon', 'type', 'admin_menu_group_id', 'admin_menu_group.name as admin_menu_group_name'])
                                  ->join('admin_menu_group', 'admin_menu_group_id = admin_menu_group.id', 'left')
                                  ->where('admin_menu.type !=', 'Child')
                                  ->where('admin_menu.status', 'Aktif')
                                  ->where('admin_menu_group.status', 'Aktif')
                                  ->orderBy('admin_menu_group.sort_order', 'ASC')
                                  ->orderBy('admin_menu.sort_order', 'ASC')
                                  ->find();

        // get array groups
        $groupList = array_column($primaryParentMenu, 'admin_menu_group_id');
        $groupList = array_unique($groupList);
        $groups    = [];

        // create group of array
        foreach ($groupList as $item):

            array_push($groups, $item);

        endforeach;

        // add menu to menu groups
        foreach ($primaryParentMenu as $key => $mainMenu):

            if ($mainMenu['type'] === 'Parent')
            {
                // get child
                $primaryParentMenu[$key]['childs'] = $this->select(['id', 'title', 'router_name', 'admin_menu_parent_id as parent_id'])
                                                          ->where('type', 'Child')
                                                          ->where('status', 'Aktif')
                                                          ->where('admin_menu_parent_id', $mainMenu['id'])
                                                          ->orderBy('sort_order', 'ASC')
                                                          ->find();
            }

            // search group key
            $groupKey = array_keys($groups, $mainMenu['admin_menu_group_id']);
            $groupKey = $groupKey[0];

            // create result if not exist
            if (!isset($result[$groupKey]))
            {
                $result[$groupKey] = [
                    'id'   => $mainMenu['admin_menu_group_id'],
                    'name' => $mainMenu['admin_menu_group_name'],
                    'menu' => []
                ];
            }

            // push to groups
            array_push($result[$groupKey]['menu'], $primaryParentMenu[$key]);

        endforeach;

        // return
        return isset($result) ? $result : [];
    }

    //================================================================================================

    protected function fixIntegerType(array $methods): array
    {
        // return if empty
        if (empty($methods['data']) OR is_null($methods['data']))
        {
            return $methods;
        }

        // check if singular or multiple
        if ($methods['singleton'])
        {
            foreach ($methods['data'] as $field => $value):

                $methods['data'][$field] = is_numeric($value) ? intval($value) : $value;

            endforeach;

        } else {

            foreach ($methods['data'] as $i => $data):

                foreach ($data as $field => $value)
                {
                    $methods['data'][$i][$field] = is_numeric($value) ? intval($value) : $value;
                }

            endforeach;
        }

        // return
        return $methods;
    }

    //======================================================================================
}
