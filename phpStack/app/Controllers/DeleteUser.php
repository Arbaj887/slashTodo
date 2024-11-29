<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use GuzzleHttp\Client;
use CodeIgniter\HTTP\ResponseInterface;

class DeleteUser extends BaseController
{
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
}
