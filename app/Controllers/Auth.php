<?php

namespace App\Controllers;

use App\Models\UserModel; // You will need to create a simple UserModel

class Auth extends BaseController
{

    public function index()
    {
        // echo password_hash("admin123", PASSWORD_DEFAULT);
        // die();
        if (session()->get('isLoggedIn')) return redirect()->to('/entries');
        return view('login');
    }

    public function login()
    {
        $db = \Config\Database::connect();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
      
        $user = $db->table('users')->where('username', $username)->get()->getRowArray();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'id'         => $user['id'],
                'username'   => $user['username'],
                'full_name'  => $user['full_name'],
                'role'       => $user['role'],
                'isLoggedIn' => true
            ]);
            return redirect()->to('/entries');
        }

        return redirect()->back()->with('error', 'चुकीचे यूजरनेम किंवा पासवर्ड!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/Auth');
    }
}
