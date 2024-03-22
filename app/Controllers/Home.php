<?php

namespace App\Controllers;

use App\Libraries\Ghivarra\VueIgniter;

class Home extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        dd($db->DBDriver);

        $vueIgniter = new VueIgniter();
        return $vueIgniter->render('MyComponent', [
            'ucing' => 'oyok',
            'myman' => 'pei',
        ]);
    }
}
