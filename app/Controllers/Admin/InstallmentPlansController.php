<?php

namespace App\Controllers\Admin;

use App\Models\InstallmentPlanModel;

class InstallmentPlansController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('plans/index', [
            'pageTitle'  => 'Installment Plans',
            'activeMenu' => 'plans',
            'canCreate'  => $this->auth->can('installment_plans.create'),
            'canUpdate'  => $this->auth->can('installment_plans.update'),
            'canDelete'  => $this->auth->can('installment_plans.delete'),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('installment_plans.view')) {
            return $denied;
        }

        $search = trim((string) $this->request->getGet('search'));
        $model = model(InstallmentPlanModel::class)->where('product_id', null);
        if ($search !== '') {
            $model->like('name', $search);
        }

        return $this->jsonSuccess('Plans loaded.', [
            'items' => $model->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('installment_plans.view')) {
            return $denied;
        }

        $row = model(InstallmentPlanModel::class)->find($id);
        if (! $row) {
            return $this->jsonError('Plan not found.', null, 404);
        }

        return $this->jsonSuccess('Plan loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('installment_plans.create')) {
            return $denied;
        }

        $data = $this->payload();
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $id = model(InstallmentPlanModel::class)->insert($data);

        return $this->jsonSuccess('Plan created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('installment_plans.update')) {
            return $denied;
        }

        $model = model(InstallmentPlanModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Plan not found.', null, 404);
        }

        $data = $this->payload();
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $model->update($id, $data);

        return $this->jsonSuccess('Plan updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('installment_plans.delete')) {
            return $denied;
        }

        $model = model(InstallmentPlanModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Plan not found.', null, 404);
        }

        $model->delete($id);

        return $this->jsonSuccess('Plan deleted.');
    }

    private function payload(): array
    {
        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return ['error' => 'Plan name is required.'];
        }

        $down = (float) $this->request->getPost('down_payment');
        $monthly = (float) $this->request->getPost('monthly_installment');
        $months = max(1, (int) $this->request->getPost('months'));
        $charges = (float) $this->request->getPost('processing_charges');
        $total = $this->request->getPost('total_payable');
        if ($total === null || $total === '') {
            $total = $down + ($monthly * $months) + $charges;
        }

        return [
            'product_id'          => null,
            'name'                => $name,
            'down_payment'        => $down,
            'monthly_installment' => $monthly,
            'months'              => $months,
            'total_payable'       => (float) $total,
            'processing_charges'  => $charges,
            'terms'               => $this->request->getPost('terms'),
            'status'              => (int) $this->request->getPost('status') === 1 ? 1 : 0,
        ];
    }
}
