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
            return redirect()->to(site_url('admin/dashboard'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
