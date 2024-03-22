<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return $this->vue->render('MyComponent', [
            'ucing' => 'oyok',
            'myman' => 'pei',
            'haha' => 'hehehee'
        ]);
    }
}
