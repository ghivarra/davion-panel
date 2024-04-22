<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Ghivarra\DavionShield;
use App\Models\WebsiteModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $moduleAlias;

    //================================================================================================
    
    public function index(): string
    {
        $websiteModel = new WebsiteModel();

        // return and render vue
        return $this->vue->render('Panel/PanelIndexView', [
            'title'   => 'Dashboard',
            'website' => $websiteModel->getAllData()
        ]);
    }

    //================================================================================================
}
