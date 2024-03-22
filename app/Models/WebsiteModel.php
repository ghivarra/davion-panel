<?php

namespace App\Models;

use CodeIgniter\Model;

class WebsiteModel extends Model
{
    protected $table            = 'website';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'value'
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

    // public functions
    public function getAllData() : array
    {
        $allData = $this->select(['name', 'value'])->find();

        if (empty($allData))
        {
            return [];
        }
        
        foreach ($allData as $item):

            $result[$item['name']] = $item['value'];

        endforeach;

        // return
        return $result;
    }

    //======================================================================================
}
