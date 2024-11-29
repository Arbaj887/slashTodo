<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\UserModel;
use GuzzleHttp\Client;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController
{
    public function login()
    {
        if (isset($_POST['email'])) {
            $user_model = new UserModel();

            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $user = $user_model->where('email', $email)->first();
            if (!$user) {
                return redirect()->back()->with('popMessage', 'Please Enter correct email and password');
                //return view('login', ['popMessage' => 'Please Enter correct email and password']);

            }
            if ($user) {
                if (password_verify($password, $user->password)) {

                    //----------------------------Creating----Session-----------------------
                    $this->session->set("user", $user);
                    //---------------------------checking--login--in--Nodejs--------------------------

                    try {
                        $client = new Client();
                        $res = $client->post('http://localhost:8000/login', [
                            'json' => [


                                'email' => $email,
                                'password' => $password
                            ]
                        ]);
                        if ($res->getStatusCode() == 200) {
                            $token = json_decode($res->getBody()->getContents());

                            $this->session->set("token", $token->token);

                            return redirect()->to('/dashboard')->with('popMessage', 'Login successful!');
                        }
                        return redirect()->back()->with('popMessage', 'Failed to Login in Nodejs. Please try again.');
                        //return view('login', ['popMessage' => 'Failed to Login. Please try again.']);

                    } catch (\Exception) {
                        return redirect()->back()->with('popMessage', 'Unable to login through MongoDB');

                        //return view('login', ['popMessage' => 'Unable to login through MongoDB']);

                    }
                } else {
                    return redirect()->back()->with('popMessage', 'please Enter valid Email and password');
                    //return view('login',['popMessage'=>'please Enter valid Email and password']);


                    //return redirect()->back()->with('error', 'Invalid password. Please try again.');
                }
            } else {
                return redirect()->back()->with('popMessage', 'please Enter valid Email and password');
                //return view('login',['popMessage'=>'please Enter valid Email and  password']);

                // return redirect()->back()->with('error', 'Invalid email. Please try again.');
            }
        }
        return view('login');
    }
}
