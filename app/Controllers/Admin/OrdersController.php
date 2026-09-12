<?php

namespace App\Controllers\Admin;

use App\Models\CustomerModel;
use App\Models\InstallmentPlanModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class OrdersController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('orders/index', [
            'pageTitle'  => 'Orders',
            'activeMenu' => 'orders',
            'canCreate'  => $this->auth->can('orders.create'),
            'canDelete'  => $this->auth->can('orders.delete'),
            'statuses'   => OrderModel::STATUSES,
        ]);
    }

    public function create()
    {
        if ($denied = $this->requirePagePermission('orders.create', 'admin/orders')) {
            return $denied;
        }

        return $this->adminView('orders/create', [
            'pageTitle'  => 'Create Order',
            'activeMenu' => 'orders',
            'canCreate'  => true,
            'plans'      => model(InstallmentPlanModel::class)
                ->where('status', 1)
                ->where('product_id IS NOT NULL', null, false)
                ->orderBy('name', 'ASC')
                ->findAll(),
            'products'   => model(ProductModel::class)->where('status', 1)->findAll(),
            'customers'  => model(CustomerModel::class)->where('status', 1)->orderBy('name')->findAll(),
        ]);
    }

    public function detail($id)
    {
        if ($denied = $this->requirePagePermission('orders.view', 'admin/orders')) {
            return $denied;
        }

        return $this->adminView('orders/detail', [
            'pageTitle'  => 'Order Details',
            'activeMenu' => 'orders',
            'canUpdate'  => $this->auth->can('orders.update'),
            'statuses'   => OrderModel::STATUSES,
            'recordId'   => (int) $id,
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('orders.view')) {
            return $denied;
        }

        $query = $this->listQuery();
        $status = trim((string) $this->request->getGet('status'));
        $dateFrom = trim((string) $this->request->getGet('date_from'));
        $dateTo = trim((string) $this->request->getGet('date_to'));

        $model = model(OrderModel::class);
        if ($query['search'] !== '') {
            $model->groupStart()
                ->like('order_number', $query['search'])
                ->orLike('customer_name', $query['search'])
                ->orLike('customer_phone', $query['search'])
                ->groupEnd();
        }
        if ($status !== '') {
            $model->where('status', $status);
        }
        if ($dateFrom !== '') {
            $model->where('created_at >=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo !== '') {
            $model->where('created_at <=', $dateTo . ' 23:59:59');
        }

        $model->orderBy('id', 'DESC');
        [$rows, $total] = $this->paginateModel($model, $query);

        $orderIds = array_map(static fn ($r) => (int) $r['id'], $rows);
        $vendorMap = [];
        if ($orderIds) {
            $vendorRows = db_connect()->table('order_items')
                ->select('order_id, vendor_name')
                ->whereIn('order_id', $orderIds)
                ->where('vendor_id IS NOT NULL', null, false)
                ->where('vendor_name IS NOT NULL', null, false)
                ->groupBy('order_id, vendor_name')
                ->get()
                ->getResultArray();
            foreach ($vendorRows as $vr) {
                $oid = (int) $vr['order_id'];
                $vendorMap[$oid][] = $vr['vendor_name'];
            }
        }

        foreach ($rows as &$row) {
            $row['status_label'] = OrderModel::STATUSES[$row['status']] ?? $row['status'];
            $row['payment_verified'] = (int) ($row['payment_verified'] ?? 0);
            $row['receipt_url'] = ! empty($row['receipt_image']) ? base_url($row['receipt_image']) : null;
            $names = array_values(array_unique(array_filter($vendorMap[(int) $row['id']] ?? [])));
            $row['vendor_names'] = $names;
            $row['vendor_label'] = $names ? implode(', ', $names) : null;
        }
        unset($row);

        if ($query['export']) {
            $csvRows = [];
            foreach ($rows as $row) {
                $csvRows[] = [
                    'order_number'   => $row['order_number'],
                    'customer_name'  => $row['customer_name'],
                    'customer_phone' => $row['customer_phone'],
                    'vendor_label'   => $row['vendor_label'] ?? '',
                    'plan_name'      => $row['plan_name'] ?? '',
                    'total_payable'  => $row['total_payable'],
                    'status'         => $row['status_label'],
                    'payment'        => $row['payment_verified'] ? 'Verified' : 'Pending',
                    'created_at'     => $row['created_at'],
                ];
            }

            return $this->csvDownload('orders.csv', [
                'order_number'   => 'Order #',
                'customer_name'  => 'Customer',
                'customer_phone' => 'Phone',
                'vendor_label'   => 'Vendor',
                'plan_name'      => 'Plan',
                'total_payable'  => 'Total',
                'status'         => 'Status',
                'payment'        => 'Payment',
                'created_at'     => 'Date',
            ], $csvRows);
        }

        return $this->jsonSuccess('Orders loaded.', $this->paginatedData($rows, $total, $query));
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('orders.view')) {
            return $denied;
        }

        $row = model(OrderModel::class)->find($id);
        if (! $row) {
            return $this->jsonError('Order not found.', null, 404);
        }

        $row['status_label'] = OrderModel::STATUSES[$row['status']] ?? $row['status'];
        $row['payment_verified'] = (int) ($row['payment_verified'] ?? 0);
        $row['receipt_url'] = ! empty($row['receipt_image']) ? base_url($row['receipt_image']) : null;
        $row['items'] = model(OrderItemModel::class)->where('order_id', $id)->findAll();
        $row['vendor_names'] = array_values(array_unique(array_filter(array_column($row['items'], 'vendor_name'))));
        $row['vendor_label'] = $row['vendor_names'] ? implode(', ', $row['vendor_names']) : null;

        return $this->jsonSuccess('Order loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('orders.create')) {
            return $denied;
        }

        $payload = $this->payload();
        if (isset($payload['error'])) {
            return $this->jsonError($payload['error']);
        }

        $db = db_connect();
        $db->transStart();

        $orderId = model(OrderModel::class)->insert($payload['order']);
        foreach ($payload['items'] as $item) {
            $item['order_id'] = $orderId;
            model(OrderItemModel::class)->insert($item);
        }

        $db->transComplete();

        return $this->jsonSuccess('Order created.', ['id' => $orderId, 'order_number' => $payload['order']['order_number']]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('orders.update')) {
            return $denied;
        }

        $model = model(OrderModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Order not found.', null, 404);
        }

        $data = [
            'customer_name'    => $this->request->getPost('customer_name'),
            'customer_email'   => $this->request->getPost('customer_email'),
            'customer_phone'   => $this->request->getPost('customer_phone'),
            'customer_cnic'    => $this->request->getPost('customer_cnic'),
            'customer_address' => $this->request->getPost('customer_address'),
            'customer_city'    => $this->request->getPost('customer_city'),
            'admin_notes'      => $this->request->getPost('admin_notes'),
            'status'           => $this->request->getPost('status') ?: 'processing',
        ];

        if (! array_key_exists($data['status'], OrderModel::STATUSES)) {
            return $this->jsonError('Invalid status.');
        }

        $model->update($id, $data);

        return $this->jsonSuccess('Order updated.');
    }

    public function updateStatus($id)
    {
        if ($denied = $this->requirePermission('orders.update')) {
            return $denied;
        }

        $model = model(OrderModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Order not found.', null, 404);
        }

        $status = (string) $this->request->getPost('status');
        if (! array_key_exists($status, OrderModel::STATUSES)) {
            return $this->jsonError('Invalid status.');
        }

        $model->update($id, [
            'status'      => $status,
            'admin_notes' => $this->request->getPost('admin_notes'),
        ]);

        return $this->jsonSuccess('Order status updated.');
    }

    public function verifyPayment($id)
    {
        if ($denied = $this->requirePermission('orders.update')) {
            return $denied;
        }

        $model = model(OrderModel::class);
        $order = $model->find($id);
        if (! $order) {
            return $this->jsonError('Order not found.', null, 404);
        }

        if (empty($order['receipt_image'])) {
            return $this->jsonError('No payment receipt uploaded for this order.');
        }

        if ((int) ($order['payment_verified'] ?? 0) === 1) {
            return $this->jsonSuccess('Payment is already verified.');
        }

        $model->update($id, [
            'payment_verified'    => 1,
            'payment_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->jsonSuccess('Payment verified successfully.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('orders.delete')) {
            return $denied;
        }

        $model = model(OrderModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Order not found.', null, 404);
        }

        $model->delete($id);

        return $this->jsonSuccess('Order archived.');
    }

    private function payload(): array
    {
        $name = trim((string) $this->request->getPost('customer_name'));
        $phone = trim((string) $this->request->getPost('customer_phone'));
        $productId = (int) $this->request->getPost('product_id');
        $planId = (int) $this->request->getPost('installment_plan_id');
        $qty = max(1, (int) $this->request->getPost('quantity'));

        if ($name === '' || $phone === '' || ! $productId) {
            return ['error' => 'Customer name, phone, and product are required.'];
        }

        $product = model(ProductModel::class)->find($productId);
        if (! $product) {
            return ['error' => 'Product not found.'];
        }

        $plan = $planId ? model(InstallmentPlanModel::class)->find($planId) : null;
        $unit = (float) $product['price'];
        $line = $unit * $qty;

        $customerId = $this->request->getPost('customer_id') ?: null;
        if (! $customerId) {
            $customerId = model(CustomerModel::class)->insert([
                'name'    => $name,
                'email'   => $this->request->getPost('customer_email'),
                'phone'   => $phone,
                'cnic'    => $this->request->getPost('customer_cnic'),
                'address' => $this->request->getPost('customer_address'),
                'city'    => $this->request->getPost('customer_city'),
                'status'  => 1,
            ]);
        }

        return [
            'order' => [
                'order_number'        => 'RS-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
                'customer_id'         => $customerId,
                'customer_name'       => $name,
                'customer_email'      => $this->request->getPost('customer_email'),
                'customer_phone'      => $phone,
                'customer_cnic'       => $this->request->getPost('customer_cnic'),
                'customer_address'    => $this->request->getPost('customer_address'),
                'customer_city'       => $this->request->getPost('customer_city'),
                'installment_plan_id' => $plan['id'] ?? null,
                'plan_name'           => $plan['name'] ?? null,
                'down_payment'        => $plan['down_payment'] ?? 0,
                'monthly_installment' => $plan['monthly_installment'] ?? 0,
                'months'              => $plan['months'] ?? 0,
                'processing_charges'  => $plan['processing_charges'] ?? 0,
                'total_payable'       => $plan['total_payable'] ?? $line,
                'subtotal'            => $line,
                'status'              => 'processing',
                'admin_notes'         => $this->request->getPost('admin_notes'),
            ],
            'items' => [[
                'product_id'   => $productId,
                'product_name' => $product['name'],
                'sku'          => $product['sku'],
                'unit_price'   => $unit,
                'quantity'     => $qty,
                'line_total'   => $line,
            ]],
        ];
    }
}
