<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;
use CodeIgniter\CodeIgniter;
use App\Models\WebsiteModel;

class Home extends BaseController
{
    public function index(): string
    {
        return 'Davion Panel is working as intended.';
    }
    
    //================================================================================================

    public function version(): Response
    {
        // load orm
        $orm = new WebsiteModel();

        // load all data
        $data = $orm->getAllData();

        // return
        return response()->setJSON([
            'codeigniter_version' => CodeIgniter::CI_VERSION,
            'app_version'         => $data['app_version'],
            'icon_version'        => $data['icon_version'],
            'logo_version'        => $data['logo_version'],
        ]);
    }

    //================================================================================================
}
