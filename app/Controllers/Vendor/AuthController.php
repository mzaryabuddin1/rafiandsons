<?php

namespace App\Controllers\Vendor;

class AuthController extends BaseVendorController
{
    public function login()
    {
        return view('vendor_panel/auth/login', [
            'pageTitle' => 'Vendor Login',
        ]);
    }

    public function attemptLogin()
    {
        $result = $this->auth->attempt(
            (string) $this->request->getPost('email'),
            (string) $this->request->getPost('password')
        );

        return $result['success']
            ? $this->jsonSuccess($result['message'], ['redirect' => $result['redirect']])
            : $this->jsonError($result['message']);
    }

    public function logout()
    {
        $this->auth->logout();

        return $this->jsonSuccess('Signed out.', ['redirect' => site_url('vendor/login')]);
    }
}
