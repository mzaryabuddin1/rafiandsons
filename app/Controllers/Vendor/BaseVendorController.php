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

    /**
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
        fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));
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
