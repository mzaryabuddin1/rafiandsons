<?php

namespace App\Controllers\Admin;

use App\Libraries\VendorMailService;
use App\Models\VendorModel;

class VendorsController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('vendors/index', [
            'pageTitle'  => 'Vendors',
            'activeMenu' => 'vendors',
            'statuses'   => VendorModel::STATUSES,
            'canUpdate'  => $this->auth->can('vendors.update'),
            'canDelete'  => $this->auth->can('vendors.delete'),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('vendors.view')) {
            return $denied;
        }

        $search = trim((string) $this->request->getGet('search'));
        $status = trim((string) $this->request->getGet('status'));
        $model = model(VendorModel::class);

        if ($search !== '') {
            $model->groupStart()
                ->like('business_name', $search)
                ->orLike('contact_name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }
        if ($status !== '' && array_key_exists($status, VendorModel::STATUSES)) {
            $model->where('status', $status);
        }

        $items = $model->orderBy('id', 'DESC')->findAll();
        foreach ($items as &$item) {
            $item['status_label'] = VendorModel::STATUSES[$item['status']] ?? $item['status'];
        }

        return $this->jsonSuccess('Vendors loaded.', ['items' => $items]);
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('vendors.view')) {
            return $denied;
        }

        $row = model(VendorModel::class)->find($id);
        if (! $row) {
            return $this->jsonError('Vendor not found.', null, 404);
        }

        $row['status_label'] = VendorModel::STATUSES[$row['status']] ?? $row['status'];
        unset($row['password']);

        return $this->jsonSuccess('Vendor loaded.', $row);
    }

    public function approve($id)
    {
        if ($denied = $this->requirePermission('vendors.update')) {
            return $denied;
        }

        $model = model(VendorModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Vendor not found.', null, 404);
        }
        if (($row['status'] ?? '') === 'approved') {
            return $this->jsonSuccess('Vendor is already approved.');
        }

        $model->update($id, [
            'status'      => 'approved',
            'admin_notes' => $this->request->getPost('admin_notes') ?: ($row['admin_notes'] ?? null),
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => $this->auth->id(),
        ]);

        $vendor = $model->find($id);
        try {
            (new VendorMailService())->sendApplicationApproved($vendor);
        } catch (\Throwable $e) {
            log_message('error', 'Vendor approval email error: ' . $e->getMessage());
        }

        return $this->jsonSuccess('Vendor approved. Account is now active.');
    }

    public function reject($id)
    {
        if ($denied = $this->requirePermission('vendors.update')) {
            return $denied;
        }

        $model = model(VendorModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Vendor not found.', null, 404);
        }

        $model->update($id, [
            'status'      => 'rejected',
            'admin_notes' => $this->request->getPost('admin_notes') ?: ($row['admin_notes'] ?? null),
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => $this->auth->id(),
        ]);

        $vendor = $model->find($id);
        try {
            (new VendorMailService())->sendApplicationRejected($vendor);
        } catch (\Throwable $e) {
            log_message('error', 'Vendor rejection email error: ' . $e->getMessage());
        }

        return $this->jsonSuccess('Vendor application rejected.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('vendors.delete')) {
            return $denied;
        }

        $model = model(VendorModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Vendor not found.', null, 404);
        }

        $model->delete($id);

        return $this->jsonSuccess('Vendor archived.');
    }
}
