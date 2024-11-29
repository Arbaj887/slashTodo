<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use GuzzleHttp\Client;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function dashboard()
    {
        if (!$this->session->get('user') && !$this->session->get('token')) {
            return redirect()->to('/login')->with('popMessage', 'Unauthorized Access');
            //return view('login',['popMessage'=>'Unauthorized Access']);
        }

        $user_model = new UserModel();
        //$user = $user_model->paginate(4);
        $user = $user_model->findAll();
        $currentPage = $this->request->getVar('page') ?? 1; // Get the current page from the request
        $perPage = 4;
        $totalUsers = count($user); // Total number of users
        $totalPages = ceil($totalUsers / $perPage); // Total number of pages
        $offset = ($currentPage - 1) * $perPage;
        $usersToDisplay = array_slice($user, $offset, $perPage); // Slice the user array   
        //------------------getting--data--from---NodeJs----------------------
        try {
            $client = new Client();


            $res = $client->get(
                'http://localhost:8000/dashboard',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->session->get('token')
                    ]

                ]
            );

            if ($res->getStatusCode() == 200) {

                $mongoUser = json_decode($res->getBody()->getContents());
                //  echo "<pre>";
                // print_r( $mongoUser);
                // echo "</pre>";
                //die;
                for ($i = 0; $i < count($user); $i++) {
                    if ($mongoUser[$i]->email === $user[$i]->email) {
                        $user[$i]->mongoId = $mongoUser[$i]->_id;
                    }
                }

                // echo "<pre>";
                // print_r(count( $user));
                // echo "</pre>";
                return view('dashboard', [
                    'users' => $usersToDisplay,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                ]);
            }
            return redirect()->to('/login')->with('popMessage', 'Please Loggin.');
            // return view('login',['popMessage'=>'Please Loggin.']);

        } catch (\Exception $e) {
            //  echo "<pre>";
            //  print_r($e);
            //  echo "</pre>";
            return redirect()->to('/login')->with('popMessage', 'Please Loggin.');
            //return view('dashboard',['popMessage'=>'Unable to get data through MongoDB']);

        }
    }
}
