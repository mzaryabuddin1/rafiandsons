<?php

namespace App\Filters;

use App\Libraries\AdminAuth;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = new AdminAuth();
        if (! $auth->check()) {
            if ($request->isAJAX() || str_contains($request->getHeaderLine('Accept'), 'application/json')) {
                return service('response')->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ]);
            }

            return redirect()->to(site_url('admin/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
