<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use GuzzleHttp\Client;
use CodeIgniter\HTTP\ResponseInterface;

class UpdateUser extends BaseController
{
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
}
