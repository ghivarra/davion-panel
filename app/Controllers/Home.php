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

        // load frontend versioning
        $packages = json_decode(file_get_contents(ROOTPATH . 'package.json'), true);

        // return
        return response()->setJSON([
            'system' => [
                'ci_version'        => CodeIgniter::CI_VERSION,
                'vue_version'       => $packages['dependencies']['vue'],
                'bootstrap_version' => $packages['dependencies']['bootstrap'],
                'vite_version'      => $packages['devDependencies']['vite'],
                'sass_version'      => $packages['devDependencies']['sass'],
            ],
            'app' => [
                'app_version'  => $data['app_version'],
                'icon_version' => $data['icon_version'],
                'logo_version' => $data['logo_version'],
            ]
        ]);
    }

    //================================================================================================
}
