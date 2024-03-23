<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Ghivarra\DavionShield;
use App\Models\WebsiteModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $websiteModel = new WebsiteModel();

        // return and render vue
        return $this->vue->render('MyComponent', [
            'title'   => 'Dashboard',
            'website' => $websiteModel->getAllData()
        ]);
    }

    //================================================================================================
}
