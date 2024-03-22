<?php

namespace App\Controllers;

use App\Libraries\Ghivarra\VueIgniter;

class Home extends BaseController
{
    public function index(): string
    {
        $vueIgniter = new VueIgniter();
        return $vueIgniter->render('MyComponent', [
            'ucing' => 'oyok',
            'myman' => 'pei',
        ]);
    }
}
