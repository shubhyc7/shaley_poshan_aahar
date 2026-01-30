<?php

namespace App\Controllers;

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
        $username = trim($this->request->getPost('username') ?? '');
        $password = $this->request->getPost('password') ?? '';

        // Validation
        if (empty($username) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'कृपया यूजरनेम आणि पासवर्ड प्रविष्ट करा!');
        }

        $db = \Config\Database::connect();
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

        return redirect()->back()->withInput()->with('error', 'चुकीचे यूजरनेम किंवा पासवर्ड!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/Auth');
    }
}
