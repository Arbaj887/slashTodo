<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use GuzzleHttp\Client;
use CodeIgniter\HTTP\ResponseInterface;

class Signup extends BaseController
{
   
    public function signup()
    {
        if (isset($_POST['name'])) {
            $user_model = new UserModel();

            $data = [
                'name' => $this->request->getPost('name'),
                'email' => strtolower($this->request->getPost('email')),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT)
            ];
            $existUser = $user = $user_model->where('email', $data['email'])->first();
            if ($existUser) {
                // echo $GLOBALS['nodeURL'];
                return redirect()->back()->with('popMessage', 'User already exists');
                //return view('signup', ['popMessage' => 'User already exists']);
            }
            $result = $user_model->save($data);
            //-------------------------------------sending--data-to--Nodejs----------------------

            if ($result) {
                try {
                    $client = new Client();
                    $res = $client->post('http://localhost:8000/register', [

                        'json' => [

                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => $this->request->getPost('password')
                        ]
                    ]);
                    if ($res->getStatusCode() == 200) {
                        //return view('login', ['popMessage' => 'User created successfully']);
                        return redirect()->to('/login')->with('popMessage', 'Registration successful! Please log in.');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('popMessage', 'Unable to Register in MongoDB');
                    //return view('signup', ['popMessage' => 'Unable to Register in MongoDB']);

                }
                return redirect()->back()->with('popMessage', 'Failed to register. Please try again.');
                //return view('signup', ['popMessage' => 'Failed to register. Please try again.']);

            } else {
                return redirect()->back()->with('popMessage', 'Failed to register. Please try again.');
                //return view('signup', ['popMessage' => 'Failed to register. Please try again.']);

            }
        }
        return view('signup');
    }
}
