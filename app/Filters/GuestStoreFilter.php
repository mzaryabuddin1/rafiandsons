<?php

namespace App\Filters;

use App\Libraries\StoreAuth;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestStoreFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = new StoreAuth();
        if ($auth->check() && strtolower($request->getMethod()) === 'get') {
            return redirect()->to(site_url('account/profile'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
