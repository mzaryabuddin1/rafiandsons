<?php

namespace App\Controllers\Admin;

class AuthController extends BaseAdminController
{
    public function login()
    {
        return view('admin/auth/login', [
            'pageTitle' => 'Admin Login',
        ]);
    }

    public function attemptLogin()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return $this->jsonError('Email and password are required.');
        }

        $result = $this->auth->attempt($email, $password);
        if (! $result['success']) {
            return $this->jsonError($result['message'], null, 401);
        }

        return $this->jsonSuccess($result['message'], ['redirect' => $result['redirect']]);
    }

    public function logout()
    {
        $this->auth->logout();

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess('Logged out.', ['redirect' => site_url('admin/login')]);
        }

        return redirect()->to(site_url('admin/login'));
    }
}
