<?php

namespace App\Filters;

use App\Libraries\VendorAuth;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestVendorFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = new VendorAuth();
        if ($auth->check() && strtolower($request->getMethod()) === 'get') {
            return redirect()->to(site_url('vendor/dashboard'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
