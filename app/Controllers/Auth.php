<?php 

namespace App\Controllers;

use App\Models\ModelAuth;

class Auth extends BaseController
{
    public function index()
    {
        // Jika sudah login, redirect ke halaman yang sesuai
        if (session()->get('logged_in')) {
            if (session()->get('role') == 'admin') {
                return redirect()->to('Admin');
            } else {
                return redirect()->to('User');
            }
        }
        return view('v_login');
    }

    public function login()
    {
        $session = session();
        $model = new ModelAuth();

        // Ambil input username dan password dari form
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cari user berdasarkan username
        $user = $model->where('username', $username)->first();

        if ($user) {
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session
                $ses_data = [
                    'id_user'   => $user['id_user'],
                    'nama_user' => $user['nama_user'],
                    'username'  => $user['username'],
                    'role'      => $user['role'], // Role untuk menentukan admin/user
                    'logged_in' => TRUE
                ];
                $session->set($ses_data);

                // Redirect berdasarkan role
                if ($user['role'] == 'admin') {
                    return redirect()->to('Admin');
                } else {
                    return redirect()->to('User');
                }
            } else {
                $session->setFlashdata('msg', 'Password salah.');
                return redirect()->to('Auth');
            }
        } else {
            $session->setFlashdata('msg', 'Username tidak ditemukan.');
            return redirect()->to('Auth');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('Auth');
    }
}