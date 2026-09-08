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

    /**
     * Shared list query params for pagination / search / CSV export.
     *
     * @return array{page:int,per_page:int,offset:int,search:string,export:bool}
     */
    protected function listQuery(int $defaultPerPage = 20, int $maxPerPage = 100): array
    {
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $perPage = (int) ($this->request->getGet('per_page') ?: $defaultPerPage);
        $perPage = max(5, min($maxPerPage, $perPage));

        return [
            'page'     => $page,
            'per_page' => $perPage,
            'offset'   => ($page - 1) * $perPage,
            'search'   => trim((string) $this->request->getGet('search')),
            'export'   => strtolower((string) $this->request->getGet('export')) === 'csv',
        ];
    }

    protected function paginatedData(array $items, int $total, array $query, array $extra = []): array
    {
        $perPage = max(1, (int) $query['per_page']);
        $page = max(1, (int) $query['page']);

        return array_merge([
            'items'     => $items,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ], $extra);
    }

    /**
     * Paginate a Query Builder that already has filters applied.
     * Caller must NOT have called get()/countAllResults() yet.
     *
     * @return array{0: list<array>, 1: int}
     */
    protected function paginateBuilder($builder, array $query, ?callable $orderBy = null): array
    {
        $total = $builder->countAllResults(false);
        if ($orderBy) {
            $orderBy($builder);
        }
        if ($query['export']) {
            $items = $builder->get(10000, 0)->getResultArray();
        } else {
            $items = $builder->get($query['per_page'], $query['offset'])->getResultArray();
        }

        return [$items, $total];
    }

    /**
     * Paginate a Model that already has filters applied.
     *
     * @return array{0: list<array>, 1: int}
     */
    protected function paginateModel($model, array $query): array
    {
        $total = $model->countAllResults(false);
        if ($query['export']) {
            $items = $model->findAll(10000, 0);
        } else {
            $items = $model->findAll($query['per_page'], $query['offset']);
        }

        return [$items, $total];
    }

    /**
     * Slice an already-built in-memory list (e.g. tree-ordered categories).
     *
     * @return array{0: list<array>, 1: int}
     */
    protected function paginateArray(array $items, array $query): array
    {
        $total = count($items);
        if ($query['export']) {
            return [array_slice($items, 0, 10000), $total];
        }

        return [array_slice($items, $query['offset'], $query['per_page']), $total];
    }

    protected function csvDownload(string $filename, array $headers, array $rows): ResponseInterface
    {
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename) ?: 'export.csv';
        if (! str_ends_with(strtolower($filename), '.csv')) {
            $filename .= '.csv';
        }

        $fh = fopen('php://temp', 'r+');
        fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
        fputcsv($fh, $headers);
        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $key => $label) {
                $field = is_int($key) ? $label : $key;
                $value = is_array($row) ? ($row[$field] ?? '') : '';
                if (is_bool($value)) {
                    $value = $value ? '1' : '0';
                } elseif (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $line[] = (string) $value;
            }
            fputcsv($fh, $line);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($csv);
    }
}
