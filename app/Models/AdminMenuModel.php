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
                                  ->where('admin_menu.status', 'Aktif')
                                  ->where('admin_menu_group.status', 'Aktif')
                                  ->orderBy('admin_menu_group.sort_order', 'ASC')
                                  ->orderBy('admin_menu.sort_order', 'ASC')
                                  ->find();
        
        foreach ($primaryParentMenu as $key => $mainMenu):

            // add is_active
            $primaryParentMenu[$key]['is_active'] = false;

            if ($mainMenu['type'] === 'Parent')
            {
                // get child
                // $childMenu = $this->select(['id', 'title', 'router_name', '']) 
            }

        endforeach;

        dd($primaryParentMenu);
    }

    //================================================================================================
}
