<?php

namespace App\Filters;

use App\Libraries\AdminAuth;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $permission = $arguments[0] ?? null;
        if (! $permission) {
            return;
        }

        $auth = new AdminAuth();
        if (! $auth->can($permission)) {
            if ($request->isAJAX() || str_contains($request->getHeaderLine('Accept'), 'application/json')) {
                return service('response')->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Forbidden.',
                ]);
            }

            return redirect()->to(site_url('admin/dashboard'))->with('error', 'You do not have permission.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
