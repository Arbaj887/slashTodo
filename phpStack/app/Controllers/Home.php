<?php

namespace App\Controllers;



class Home extends BaseController
{
    //const nodeURL='http://localhost:8000';
    public function index(): string
    {
        // return view('popMessage');
        return view('login');
    }


    

    
    
}
