<?php

namespace App\Controllers;

use App\Models\WebsiteModel;

class Home extends BaseController
{
    public function index(): string
    {
        $websiteModel = new WebsiteModel();

        // return render vue
        return $this->vue->render('MyComponent', [
            'webConfig' => $websiteModel->getAllData()
        ]);
    }
}
