<?php

namespace App\Controllers;

use App\Models\UserModel;
use GuzzleHttp\Client;

class Home extends BaseController
{
     //const nodeURL='http://localhost:8000';
    public function index(): string
    {
        return view('login');
    }

          


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
                echo "user Already Exist";
                return;
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
                        return redirect()->to('/login')->with('success', 'Registration successful! Please log in.');
                    }
                } catch (\Exception) {
                    echo "Unable to Register in MongoDB";
                }
                return redirect()->back()->with('error', 'Failed to register. Please try again.');
            } else {
                return redirect()->back()->with('error', 'Failed to register. Please try again.');
            }
        }
        return view('signup'); 
    }

    //------------------------------------------------------Login--------------------------------------------------------------
    public function login()
    {
        if (isset($_POST['email'])) {
            $user_model = new UserModel();
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $user = $user_model->where('email', $email)->first();
            if (!$user) {
                echo "Please Enter correct email and password";
                return;
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
                            return redirect()->to('/dashboard')->with('success', 'Login successful! Please log in.');
                        }

                        return redirect()->back()->with('error', 'Failed to Login. Please try again.');
                    } catch (\Exception) {
                        echo "Unable to login through MongoDB";
                    }
                } else {
                    return redirect()->back()->with('error', 'Invalid password. Please try again.');
                }
            } else {

                return redirect()->back()->with('error', 'Invalid email. Please try again.');
            }
        }
        return view('login');
    }

    //----------------------------------------------Logout----------------------------------------------------------
    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/login');
    }

    //-----------------------------------------------Dasdboard-----------------------------------------------

    public function dashboard()
    {
        if (!$this->session->get('user') && !$this->session->get('token')) {
            return redirect()->to('/login');
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
                //print_r( $mongoUser[0]->_id);
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

            return redirect()->to("/login")->with('error', 'Please Loggin.');
        } catch (\Exception $e) {
            //  echo "<pre>";
            //  print_r($e);
            //  echo "</pre>";
            echo "Unable to get data through MongoDB";
        }
    }
    //-------------------------------------------------Edit---User--------------------------------------------------
    public function updateUser()
    {
        if (!$this->session->get('user')) {
            return redirect()->to('/login');
        } 
        if (isset($_POST['updateUser'])) {
            $user_model = new UserModel();
            $updatedUser = [];

            $id = $this->request->getPost('editId');
            $mongoId =  $this->request->getPost('mongoId');
            $name = $this->request->getPost('editName');
            $email = strtolower($this->request->getPost('editEmail'));


            if ($name) {
                $updatedUser['name'] = $name;
            }
            if ($email) {
                $updatedUser['email'] = $email;
            }


            $result = $user_model->update($id, $updatedUser);
            if ($result) {
                //-------------------------------Updateing--in---NodeJs-------------------------
                try {
                    $client = new Client();
                    $res = $client->post('http://localhost:8000/edit', [

                        'headers' => [
                            'Authorization' => 'Bearer ' . $this->session->get('token')
                        ],
                        'json' => [


                            'id' => $mongoId,
                            'name' => $name,
                            'email' => $email,

                        ]
                    ]);
                } catch (\Exception $e) {
                    echo "Unable to Update user in MongoDB";
                }
                echo "User updated successfully";
                return redirect()->to('/dashboard');
            }
        } else {
            echo "Unable to Update";
        }
    }
    //---------------------------------------------------Delete--User---------------------------------------------------------    

    public function deleteuser()
    {
        if (!$this->session->get('user')) {
            return redirect()->to('/login');
        }
        $id = $this->request->getUri()->getSegment(2);
        $mongoId = $this->request->getUri()->getSegment(3);
        $user_model = new UserModel();

        $result = $user_model->delete($id);
        if ($result) {
            try {
                $client = new Client();
                $res = $client->post('http://localhost:8000/delete', [

                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->session->get('token')
                    ],
                    'json' => [


                        'id' => $mongoId,


                    ]
                ]);
            } catch (\Exception $e) {
                echo "Unable to Delete user in MongoDB";
            }
            echo "User deleted successfully";
            return redirect()->to('/dashboard');
        } else {
            echo "Unable to Delete";
        }
    }
}
