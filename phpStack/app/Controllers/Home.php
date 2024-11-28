<?php

namespace App\Controllers;

use App\Models\UserModel;
use GuzzleHttp\Client;

class Home extends BaseController
{
     //const nodeURL='http://localhost:8000';
    public function index(): string
    {
        // return view('popMessage');
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

    //------------------------------------------------------Login--------------------------------------------------------------
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

    //----------------------------------------------Logout----------------------------------------------------------
    public function logout()
    {
         $this->session->remove('user');
         $this->session->remove('token');
        return redirect()->to('/login')->with('popMessage', 'You have been logged out');
       // return view('login',['popMessage'=>'You have been logged out']);
    }

    //-----------------------------------------------Dasdboard-----------------------------------------------

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
                // die;
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
    //-------------------------------------------------Edit---User--------------------------------------------------
    public function updateUser()
    {
        if (!$this->session->get('user')) {
            return redirect()->to('/login')->with('popMessage', 'Unauthorized Access');
            //return view('login',['popMessage'=>'Unauthorized Access']);
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
                    return redirect()->back()->with('popMessage', 'Unable to Update user in MongoDB');
                   // return view('login',['popMessage'=>'Unable to Update user in MongoDB']);
                    
                }
                return redirect()->back()->with('popMessage', 'User updated successfully');
                   
                //return view('dashboard',['popMessage'=>'User updated successfully']);
                //echo "User updated successfully";
                
            }
        } else {
            return redirect()->back()->with('popMessage', 'Unable to Update user');
            //return view('dashboard',['popMessage'=>'Unable to Update']);
            
        }
    }
    //---------------------------------------------------Delete--User---------------------------------------------------------    

    public function deleteuser()
    {
        if (!$this->session->get('user')) {
            return redirect()->to('/login')->with('popMessage', 'Unauthorized Access');
           
          //  return view('login',['popMessage'=>'Unauthorized Access']);
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
                return redirect()->back()->with('popMessage', 'Unable to Delete user in MongoDB');
           
                //return view('dashboard',['popMessage'=>'Unable to Delete user in MongoDB']);
                
            }
            return redirect()->back()->with('popMessage', 'User deleted successfully');
           
            //return view('dashboard',['popMessage'=>'User deleted successfully']);
            
           
        } else {
            return redirect()->back()->with('popMessage', 'Unable to Delete user');
            //return view('dashboard',['popMessage'=>'Unable to Delete']);
            
        }
    }

    // ----------------------------------------Upload--File--of--excel------------------------------------
    public function uploadfile() {
        // Check if the user is authenticated
        if (!$this->session->get('user') || !$this->session->get('token')) {
            return redirect()->to('/login')->with('popMessage', 'Unauthorized Access');
        }
    
        // Check if the form was submitted
        if (isset($_POST['UploadFile'])) {
            $file = $this->request->getFile('selectFile');
    
            // Validate the uploaded file
            if (!$file || !$file->isValid()) {
                return redirect()->back()->with('popMessage', 'Please select a valid file');
            }
    
            $file_name = $file->getRandomName();
            $filePath = WRITEPATH . 'uploads/' . $file_name;
    
            try {
                // Move the uploaded file
                $file->move(WRITEPATH . 'uploads', $file_name);
            } catch (\Exception $e) {
                return redirect()->back()->with('popMessage', 'Unable to upload file in WRITEPATH: ' . $e->getMessage());
            }
    
            // Open the file for reading
            if (($fileHandle = fopen($filePath, 'r')) === false) {
                return redirect()->back()->with('popMessage', 'Unable to open the uploaded file');
            }
    
            $user_model = new UserModel();
            $header = fgetcsv($fileHandle);
            if ($header === false) {
                fclose($fileHandle);
                return redirect()->back()->with('popMessage', 'Error reading CSV header');
            }
    
            while (($row = fgetcsv($fileHandle)) !== false) {
                if (count($row) !== count($header)) {
                    fclose($fileHandle);
                    return redirect()->back()->with('popMessage', 'Error in CSV data format');
                }
    
                $data = array_combine($header, $row);
                if ($data === false) {
                    fclose($fileHandle);
                    return redirect()->back()->with('popMessage', 'Error in CSV data format');
                }
    
                // Validate and hash the password
                if (isset($data['password']) && !empty($data['password'])) {
                    $password_notHash = $data['password'];
                    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                } else {
                    fclose($fileHandle);
                    return redirect()->back()->with('popMessage', 'Password is required');
                }
    
                // Save the user data
                $result = $user_model->save($data);
                if ($result) {
                    // Upload to Node.js
                    try {
                        $client = new Client();
                        $res = $client->post('http://localhost:8000/register', [
                            'json' => [
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'password' => $password_notHash
                            ]
                        ]);
    
                        if ($res->getStatusCode() !== 200) {
                            fclose($fileHandle);
                            return redirect()->back()->with('popMessage', 'Failed to upload on Node.js: ' . $res->getBody());
                        }
                    } catch (\Exception $e) {
                        fclose($fileHandle);
                        return redirect()->back()->with('popMessage', 'Unable to upload file on Node.js: ' . $e->getMessage());
                    }
                } else {
                    fclose($fileHandle);
                    return redirect()->back()->with('popMessage', 'Failed to upload on MySQL');
                }
            }
    
            fclose($fileHandle);
            return redirect()->back()->with('popMessage', 'File processed successfully');
        } else {
            return redirect()->back()->with('popMessage', 'Unable to Upload data');
        }
    }
}
