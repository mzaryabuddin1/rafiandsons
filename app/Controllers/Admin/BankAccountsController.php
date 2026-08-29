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

    public function list()
    {
        if ($denied = $this->requirePermission('bank_accounts.view')) {
            return $denied;
        }

        $items = model(BankAccountModel::class)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($items as &$item) {
            $item['logo_url'] = ! empty($item['logo']) ? base_url($item['logo']) : null;
        }

        return $this->jsonSuccess('Bank accounts loaded.', ['items' => $items]);
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
