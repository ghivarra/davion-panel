<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModuleModel;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;
use Faker\Factory;

class ModuleController extends BaseController
{
    public function datatable(): ResponseInterface
    {
        // create model instance
        $adminModuleModel = new AdminModuleModel();

        // get input
        $draw    = $this->request->getPost('draw');
        $all     = $this->request->getPost('all');
        $limit   = intval($this->request->getPost('limit'));
        $offset  = intval($this->request->getPost('offset'));
        $order   = $this->request->getPost('order');
        $columns = $this->request->getPost('columns');

        // set order column and dir
        $defaultOrderCol = 'name';
        $defaultOrderDir = 'asc';
        $orderColumn     = isset($order['column']) ? $order['column'] : 'name';
        $orderDir        = isset($order['dir']) ? strtoupper($order['dir']) : 'ASC';

        // get total
        $total = $adminModuleModel->countAllResults();

        // no query
        $orm = $adminModuleModel->select(['alias', 'name', 'group', 'status'])
                                ->orderBy($orderColumn, $orderDir)
                                ->orderBy($defaultOrderCol, $defaultOrderDir);
        
        if ($all !== 'true')
        {
            $orm = $orm->limit($limit, $offset);
        }

        // get filtered total
        $filteredTotal = $orm->countAllResults();

        // build query again
        $orm = $adminModuleModel->select(['alias', 'name', 'group', 'status'])
                                ->orderBy($orderColumn, $orderDir)
                                ->orderBy($defaultOrderCol, $defaultOrderDir);
        
        if ($all !== 'true')
        {
            $orm = $orm->limit($limit, $offset);
        }

        // get data
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

    public function seed($total): String
    {
        $faker = Factory::create();
        $group = ['Website', 'Admin', 'Public', 'Module', 'Menu'];

        for ($i=0; $i < $total; $i++)
        {
            $groupId  = random_int(0, 4);
            $data[$i] = [
                'alias'  => $faker->uuid(),
                'name'   => $faker->words(2, true),
                'group'  => $group[$groupId],
                'status' => 'Aktif'
            ];
        }

        // input
        $orm = new AdminModuleModel();
        $orm->insertBatch($data);

        // return
        return 'OK';
    }

    //================================================================================================
}
