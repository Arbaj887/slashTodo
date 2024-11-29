<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use GuzzleHttp\Client;
use CodeIgniter\HTTP\ResponseInterface;

class Uploadfile extends BaseController
{
    public function uploadfile()
    {
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
                // echo "<pre>";
                // print_r($row);
                // echo "</pre>"; die;
                if (count($row) !== count($header)) {
                    fclose($fileHandle);
                    return redirect()->back()->with('popMessage', 'Error in CSV data format');
                }

                $data = array_combine($header, $row);
                if ($data === false) {
                    fclose($fileHandle);
                    return redirect()->back()->with('popMessage', 'Error in CSV data format');
                }

                // check double entry of user 
                $existUser = $user_model->where('email', $data['email'])->first();
                if ($existUser) {
                    continue;
                    //return redirect()->back()->with('popMessage', 'User Already Exist:');

                }
                if (empty($data['name']) || empty($data['email'])  || empty($data['password'])) {

                    // This will handle the case where any of the fields are empty or not set
                    //  echo "<pre>";
                    //  print_r($data['email'].''.''.$data['password']);
                    //  echo "</pre>"; 
                    //  die;
                    $emptyEntry = WRITEPATH . 'uploads/invalid' . time() . '.csv';
                    $file = fopen($emptyEntry, 'a');

                    //  echo "<pre>";
                    //  print_r(filesize($emptyEntry));
                    //  echo "</pre>"; die;
                    if (filesize($emptyEntry) === 0) {
                        fputcsv($file, $header);
                    } // Write the header to the CSV file
                    fputcsv($file, $row);    // Write the row of data to the CSV file

                    // Combine the header and the row into an associative array
                    //$invalidField = array_combine($header, $row);

                    // Optionally, you can log or process $invalidField here

                    fclose($file); // Close the file after writing


                    continue; // Skip to the next iteration of the loop
                }
                // Validate and hash the password
                if (isset($data['password']) && !empty($data['password'])) {
                    $password_notHash = $data['password'];
                    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                } else {
                    fclose($fileHandle);
                    return redirect()->back()->with('popMessage', 'Password is required');
                }


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

            // unlink($filePath); ///---remove file after data saved to database
            return redirect()->back()->with('popMessage', 'File processed successfully');
        } else {
            return redirect()->back()->with('popMessage', 'Unable to Upload data');
        }
    }
}
