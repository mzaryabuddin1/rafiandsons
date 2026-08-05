<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AdminAuth;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseAdminController extends BaseController
{
    protected AdminAuth $auth;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->auth = new AdminAuth();
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

    protected function requirePermission(string $permission): ?ResponseInterface
    {
        if (! $this->auth->can($permission)) {
            return $this->jsonError('You do not have permission for this action.', null, 403);
        }

        return null;
    }

    protected function adminView(string $view, array $data = [])
    {
        $data['authUser'] = $this->auth->user();
        $data['auth']     = $this->auth;
        $data['pageTitle'] = $data['pageTitle'] ?? 'Admin';
        $data['activeMenu'] = $data['activeMenu'] ?? '';

        return view('admin/' . $view, $data);
    }

    protected function makeSlug(string $text, string $table, ?int $ignoreId = null): string
    {
        $slug = url_title($text, '-', true);
        $db = db_connect();
        $base = $slug;
        $i = 1;
        while (true) {
            $builder = $db->table($table)->where('slug', $slug);
            if ($ignoreId) {
                $builder->where('id !=', $ignoreId);
            }
            if ($builder->countAllResults() === 0) {
                break;
            }
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    protected function storeUpload(string $field, string $folder): ?string
    {
        $file = $this->request->getFile($field);
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $target = FCPATH . 'uploads/' . $folder;
        if (! is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $name = $file->getRandomName();
        $file->move($target, $name);

        return 'uploads/' . $folder . '/' . $name;
    }
}
