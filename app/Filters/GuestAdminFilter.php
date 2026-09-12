<?php

namespace App\Filters;

use App\Libraries\AdminAuth;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestAdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = new AdminAuth();
        if ($auth->check() && strtolower($request->getMethod()) === 'get') {
            helper('admin');

            return redirect()->to(admin_url('dashboard'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
