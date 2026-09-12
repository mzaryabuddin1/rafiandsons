<?php

namespace App\Controllers\Admin;

use App\Models\CustomerModel;
use App\Models\OrderModel;

class CustomersController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('customers/index', [
            'pageTitle'  => 'Customers',
            'activeMenu' => 'customers',
            'canCreate'  => $this->auth->can('customers.create'),
            'canUpdate'  => $this->auth->can('customers.update'),
            'canDelete'  => $this->auth->can('customers.delete'),
        ]);
    }

    public function create()
    {
        if ($denied = $this->requirePagePermission('customers.create', 'admin/customers')) {
            return $denied;
        }

        return $this->adminView('customers/form', [
            'pageTitle'  => 'Add Customer',
            'activeMenu' => 'customers',
            'isEdit'     => false,
            'recordId'   => null,
        ]);
    }

    public function edit($id)
    {
        if ($denied = $this->requirePagePermission('customers.update', 'admin/customers')) {
            return $denied;
        }

        return $this->adminView('customers/form', [
            'pageTitle'  => 'Edit Customer',
            'activeMenu' => 'customers',
            'isEdit'     => true,
            'recordId'   => (int) $id,
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('customers.view')) {
            return $denied;
        }

        $query = $this->listQuery();
        $registered = $this->request->getGet('registered');
        $model = model(CustomerModel::class);
        if ($query['search'] !== '') {
            $model->groupStart()
                ->like('name', $query['search'])
                ->orLike('email', $query['search'])
                ->orLike('phone', $query['search'])
                ->groupEnd();
        }

        if ($registered === '1') {
            $model->where('password IS NOT NULL', null, false)
                ->where('password !=', '');
        } elseif ($registered === '0') {
            $model->groupStart()
                ->where('password', null)
                ->orWhere('password', '')
            ->groupEnd();
        }

        $model->orderBy('id', 'DESC');
        [$items, $total] = $this->paginateModel($model, $query);
        foreach ($items as &$item) {
            $item['is_registered'] = $this->isRegisteredCustomer($item);
            unset($item['password']);
        }
        unset($item);

        if ($query['export']) {
            $csvRows = [];
            foreach ($items as $item) {
                $csvRows[] = [
                    'id'      => $item['id'],
                    'name'    => $item['name'],
                    'phone'   => $item['phone'] ?? '',
                    'email'   => $item['email'] ?? '',
                    'city'    => $item['city'] ?? '',
                    'account' => ! empty($item['is_registered']) ? 'Registered' : 'Guest',
                    'status'  => (int) ($item['status'] ?? 0) === 1 ? 'Active' : 'Inactive',
                ];
            }

            return $this->csvDownload('customers.csv', [
                'id'      => 'ID',
                'name'    => 'Name',
                'phone'   => 'Phone',
                'email'   => 'Email',
                'city'    => 'City',
                'account' => 'Account',
                'status'  => 'Status',
            ], $csvRows);
        }

        return $this->jsonSuccess('Customers loaded.', $this->paginatedData($items, $total, $query));
    }

    private function isRegisteredCustomer(array $customer): bool
    {
        return trim((string) ($customer['password'] ?? '')) !== '';
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('customers.view')) {
            return $denied;
        }

        $row = model(CustomerModel::class)->find($id);
        if (! $row) {
            return $this->jsonError('Customer not found.', null, 404);
        }

        $orders = model(OrderModel::class)->where('customer_id', $id)->orderBy('id', 'DESC')->findAll(20);
        $row['is_registered'] = $this->isRegisteredCustomer($row);
        unset($row['password']);
        $row['orders'] = $orders;

        return $this->jsonSuccess('Customer loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('customers.create')) {
            return $denied;
        }

        $data = $this->payload();
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $id = model(CustomerModel::class)->insert($data);

        return $this->jsonSuccess('Customer created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('customers.update')) {
            return $denied;
        }

        $model = model(CustomerModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Customer not found.', null, 404);
        }

        $data = $this->payload();
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $model->update($id, $data);

        return $this->jsonSuccess('Customer updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('customers.delete')) {
            return $denied;
        }

        $model = model(CustomerModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Customer not found.', null, 404);
        }

        $model->delete($id);

        return $this->jsonSuccess('Customer deleted.');
    }

    private function payload(): array
    {
        $name = trim((string) $this->request->getPost('name'));
        $phone = trim((string) $this->request->getPost('phone'));
        if ($name === '' || $phone === '') {
            return ['error' => 'Name and phone are required.'];
        }

        return [
            'name'    => $name,
            'email'   => $this->request->getPost('email'),
            'phone'   => $phone,
            'cnic'    => $this->request->getPost('cnic'),
            'address' => $this->request->getPost('address'),
            'city'    => $this->request->getPost('city'),
            'notes'   => $this->request->getPost('notes'),
            'status'  => (int) $this->request->getPost('status') === 1 ? 1 : 0,
        ];
    }
}
