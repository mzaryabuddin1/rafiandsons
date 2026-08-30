<?php

namespace App\Controllers\Vendor;

use App\Controllers\BaseController;
use App\Libraries\VendorAuth;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseVendorController extends BaseController
{
    protected VendorAuth $auth;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->auth = new VendorAuth();
        helper(['url', 'form', 'text']);
    }

    protected function jsonSuccess(string $message = 'OK', $data = null, int $code = 200): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function jsonError(string $message = 'Error', $data = null, int $code = 400): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function vendorView(string $view, array $data = [])
    {
        $data['authUser']   = $this->auth->user();
        $data['auth']       = $this->auth;
        $data['pageTitle']  = $data['pageTitle'] ?? 'Vendor Panel';
        $data['activeMenu'] = $data['activeMenu'] ?? '';

        return view('vendor_panel/' . $view, $data);
    }
}
