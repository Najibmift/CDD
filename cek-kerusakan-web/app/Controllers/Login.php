<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        // Tampilkan halaman login
        return view('login');
    }

    public function auth()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                // Login berhasil
                session()->set([
                    'user_id' => $user['id'], // kalau kamu punya kolom ID di tabel user
                    'username' => $user['username'],
                    'nama' => $user['nama'],
                    'logged_in' => true
                ]);
                return redirect()->to('/dashboard');
            } else {
                // Password salah
                return redirect()->back()->with('error', 'Username atau Password salah!');
            }
        } else {
            // Username tidak ditemukan
            return redirect()->back()->with('error', 'Username atau Password salah!');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
