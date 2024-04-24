<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModuleModel;
use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;

class ModuleController extends BaseController
{
    private function buildSearchQuery($orm, $columns)
    {
        foreach ($columns as $column):

            if (!empty($column['query']))
            {
                if ($column['key'] === 'status')
                {
                    $orm->where($column['key'], $column['query']);

                } else {

                    if (str_contains($column['key'], '.'))
                    {
                        $orm->like($column['key'], $column['query'], 'both', null, true);
    
                    } else {
                        
                        $orm->like("admin_module.{$column['key']}", $column['query'], 'both', null, true);
                    }
                }
            }

        endforeach;

        return $orm;
    }

    //================================================================================================

    public function datatable(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleDatatable');
        
        if (!$permission)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
        }

        // create model instance
        $orm = new AdminModuleModel();

        // get input
        $draw    = $this->request->getPost('draw');
        $all     = $this->request->getPost('all');
        $limit   = intval($this->request->getPost('limit'));
        $offset  = intval($this->request->getPost('offset'));
        $order   = $this->request->getPost('order');
        $columns = $this->request->getPost('columns');
        $select  = ['alias', 'name', 'group', 'status'];

        // set order column and dir
        $defaultOrderCol = 'name';
        $defaultOrderDir = 'asc';
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
        $filteredTotal = $orm->countAllResults();

        // build query again
        $orm = $orm->select($select)
                   ->orderBy($orderColumn, $orderDir)
                   ->orderBy($defaultOrderCol, $defaultOrderDir);
        
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

    public function groupList(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleDatatable');

        if (!$permission)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
        }

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

    public function create(): ResponseInterface
    {
        $permission = $this->checkPermission('moduleCreate');
        
        if (!$permission)
        {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini'
            ]);
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
}
