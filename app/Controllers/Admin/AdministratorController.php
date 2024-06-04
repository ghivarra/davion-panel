<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Ghivarra\DavionShield;
use App\Models\AdminModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdministratorController extends BaseController
{
    private function buildSearchQuery($orm, $columns)
    {
        foreach ($columns as $column):

            if ($column['key'] === 'status' OR $column['key'] === 'name')
            {
                $column['key'] = "admin.{$column['key']}";
            }

            if (strlen($column['query']) > 0)
            {
                if ($column['key'] === 'admin.status' OR $column['key'] === 'is_superadmin')
                {
                    $orm->where($column['key'], $column['query']);

                } elseif ($column['key'] === 'email_verified_at') {

                    $query = empty($column['query']) ? 'IS NULL' : 'IS NOT NULL';
                    $orm->where("{$column['key']} {$query}");

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

    public function datatable(): ResponseInterface
    {
        $permission = $this->checkPermission('adminView');
        
        if (!$permission)
        {
            return cannotAccessModule();
        }

        // create model instance
        $orm = new AdminModel();

        // get input
        $draw    = $this->request->getPost('draw');
        $all     = $this->request->getPost('all');
        $limit   = intval($this->request->getPost('limit'));
        $offset  = intval($this->request->getPost('offset'));
        $order   = $this->request->getPost('order');
        $columns = $this->request->getPost('columns');
        $select  = ['admin.id', 'admin.name', 'username', 'is_superadmin', 'admin.status', 'email', 'email_verified_at', 'admin_role_id', 'photo'];

        // set order column and dir
        $defaultOrderCol = 'name';
        $defaultOrderDir = 'ASC';
        $orderColumn     = isset($order['column']) ? $order['column'] : 'name';
        $orderDir        = isset($order['dir']) ? strtoupper($order['dir']) : 'ASC';

        // get total
        $total = $orm->countAllResults();

        // no query
        $orm = $orm->select($select)
                   ->join('admin_role', 'admin_role_id = admin_role.id', 'left')
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

        // parse data
        foreach ($data as $key => $item):

            $data[$key]['email_verified_at'] = is_null($item['email_verified_at']) ? 'Belum Verifikasi' : 'Terverifikasi';

        endforeach;

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
}
