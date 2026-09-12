<?php

namespace App\Controllers\Admin;

use App\Models\BankAccountModel;

class BankAccountsController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('bank_accounts/index', [
            'pageTitle'  => 'Bank Accounts',
            'activeMenu' => 'bank_accounts',
            'canCreate'  => $this->auth->can('bank_accounts.create'),
            'canUpdate'  => $this->auth->can('bank_accounts.update'),
            'canDelete'  => $this->auth->can('bank_accounts.delete'),
        ]);
    }

    public function create()
    {
        if ($denied = $this->requirePagePermission('bank_accounts.create', 'admin/bank-accounts')) {
            return $denied;
        }

        return $this->adminView('bank_accounts/form', [
            'pageTitle'  => 'Add Bank Account',
            'activeMenu' => 'bank_accounts',
            'isEdit'     => false,
            'recordId'   => null,
        ]);
    }

    public function edit($id)
    {
        if ($denied = $this->requirePagePermission('bank_accounts.update', 'admin/bank-accounts')) {
            return $denied;
        }

        return $this->adminView('bank_accounts/form', [
            'pageTitle'  => 'Edit Bank Account',
            'activeMenu' => 'bank_accounts',
            'isEdit'     => true,
            'recordId'   => (int) $id,
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('bank_accounts.view')) {
            return $denied;
        }

        $query = $this->listQuery();
        $model = model(BankAccountModel::class);
        if ($query['search'] !== '') {
            $model->groupStart()
                ->like('bank_name', $query['search'])
                ->orLike('account_title', $query['search'])
                ->orLike('account_number', $query['search'])
                ->orLike('iban', $query['search'])
                ->orLike('branch', $query['search'])
                ->groupEnd();
        }

        $model->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
        [$items, $total] = $this->paginateModel($model, $query);

        foreach ($items as &$item) {
            $item['logo_url'] = ! empty($item['logo']) ? base_url($item['logo']) : null;
        }
        unset($item);

        if ($query['export']) {
            $csvRows = [];
            foreach ($items as $item) {
                $csvRows[] = [
                    'bank_name'      => $item['bank_name'],
                    'account_title'  => $item['account_title'],
                    'account_number' => $item['account_number'],
                    'iban'           => $item['iban'] ?? '',
                    'branch'         => $item['branch'] ?? '',
                    'sort_order'     => $item['sort_order'] ?? 0,
                    'status'         => (int) ($item['status'] ?? 0) === 1 ? 'Active' : 'Inactive',
                ];
            }

            return $this->csvDownload('bank-accounts.csv', [
                'bank_name'      => 'Bank',
                'account_title'  => 'Account Title',
                'account_number' => 'Account Number',
                'iban'           => 'IBAN',
                'branch'         => 'Branch',
                'sort_order'     => 'Sort',
                'status'         => 'Status',
            ], $csvRows);
        }

        return $this->jsonSuccess('Bank accounts loaded.', $this->paginatedData($items, $total, $query));
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('bank_accounts.view')) {
            return $denied;
        }

        $row = model(BankAccountModel::class)->find($id);
        if (! $row) {
            return $this->jsonError('Bank account not found.', null, 404);
        }

        $row['logo_url'] = ! empty($row['logo']) ? base_url($row['logo']) : null;

        return $this->jsonSuccess('Bank account loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('bank_accounts.create')) {
            return $denied;
        }

        $data = $this->payload();
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $id = model(BankAccountModel::class)->insert($data);

        return $this->jsonSuccess('Bank account created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('bank_accounts.update')) {
            return $denied;
        }

        $model = model(BankAccountModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Bank account not found.', null, 404);
        }

        $data = $this->payload($row);
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $model->update($id, $data);

        return $this->jsonSuccess('Bank account updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('bank_accounts.delete')) {
            return $denied;
        }

        $model = model(BankAccountModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Bank account not found.', null, 404);
        }

        if (! empty($row['logo']) && str_starts_with($row['logo'], 'uploads/')) {
            $full = FCPATH . $row['logo'];
            if (is_file($full)) {
                @unlink($full);
            }
        }

        $model->delete($id);

        return $this->jsonSuccess('Bank account deleted.');
    }

    private function payload(?array $existing = null): array
    {
        $bankName = trim((string) $this->request->getPost('bank_name'));
        $accountTitle = trim((string) $this->request->getPost('account_title'));
        $accountNumber = trim((string) $this->request->getPost('account_number'));

        if ($bankName === '' || $accountTitle === '' || $accountNumber === '') {
            return ['error' => 'Bank name, account title, and account number are required.'];
        }

        $data = [
            'bank_name'      => $bankName,
            'account_title'  => $accountTitle,
            'account_number' => $accountNumber,
            'iban'           => trim((string) $this->request->getPost('iban')) ?: null,
            'branch'         => trim((string) $this->request->getPost('branch')) ?: null,
            'sort_order'     => (int) $this->request->getPost('sort_order'),
            'status'         => (int) $this->request->getPost('status') === 1 ? 1 : 0,
        ];

        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $logoFile->isValid() || $logoFile->hasMoved()) {
                return ['error' => 'Invalid bank logo file.'];
            }

            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (! in_array($logoFile->getMimeType(), $allowed, true)) {
                return ['error' => 'Bank logo must be an image (JPG, PNG, WEBP, or GIF).'];
            }

            $logo = $this->storeUpload('logo', 'banks');
            if (! $logo) {
                return ['error' => 'Could not upload bank logo.'];
            }

            if ($existing && ! empty($existing['logo']) && str_starts_with($existing['logo'], 'uploads/')) {
                $old = FCPATH . $existing['logo'];
                if (is_file($old)) {
                    @unlink($old);
                }
            }

            $data['logo'] = $logo;
        } elseif ($existing) {
            $data['logo'] = $existing['logo'] ?? null;
        }

        return $data;
    }
}
