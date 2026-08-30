<?php

namespace App\Controllers\Vendor;

use App\Models\OrderItemModel;
use App\Models\OrderModel;

class OrdersController extends BaseVendorController
{
    public function index()
    {
        return $this->vendorView('orders/index', [
            'pageTitle'  => 'Orders',
            'activeMenu' => 'orders',
            'statuses'   => OrderModel::STATUSES,
        ]);
    }

    public function list()
    {
        $vendorId = (int) $this->auth->id();
        $search = trim((string) $this->request->getGet('search'));
        $status = trim((string) $this->request->getGet('status'));

        $itemRows = model(OrderItemModel::class)
            ->where('vendor_id', $vendorId)
            ->orderBy('id', 'DESC')
            ->findAll();

        $orderIds = array_values(array_unique(array_map('intval', array_column($itemRows, 'order_id'))));
        if (! $orderIds) {
            return $this->jsonSuccess('Orders loaded.', ['items' => []]);
        }

        $model = model(OrderModel::class)->whereIn('id', $orderIds);
        if ($search !== '') {
            $model->groupStart()
                ->like('order_number', $search)
                ->orLike('customer_name', $search)
                ->orLike('customer_phone', $search)
                ->groupEnd();
        }
        if ($status !== '' && array_key_exists($status, OrderModel::STATUSES)) {
            $model->where('status', $status);
        }

        $orders = $model->orderBy('id', 'DESC')->findAll();

        $itemsByOrder = [];
        foreach ($itemRows as $item) {
            $itemsByOrder[(int) $item['order_id']][] = $item;
        }

        $rows = [];
        foreach ($orders as $order) {
            $vendorItems = $itemsByOrder[(int) $order['id']] ?? [];
            $rows[] = [
                'id'            => $order['id'],
                'order_number'  => $order['order_number'],
                'customer_name' => $order['customer_name'],
                'customer_phone'=> $order['customer_phone'],
                'status'        => $order['status'],
                'status_label'  => OrderModel::STATUSES[$order['status']] ?? $order['status'],
                'created_at'    => $order['created_at'] ?? '',
                'item_count'    => count($vendorItems),
                'vendor_total'  => array_sum(array_map(
                    static fn ($row) => (float) ($row['line_total'] ?? 0),
                    $vendorItems
                )),
            ];
        }

        return $this->jsonSuccess('Orders loaded.', ['items' => $rows]);
    }

    public function show($id)
    {
        $vendorId = (int) $this->auth->id();
        $order = model(OrderModel::class)->find($id);
        if (! $order) {
            return $this->jsonError('Order not found.', null, 404);
        }

        $items = model(OrderItemModel::class)
            ->where('order_id', $id)
            ->where('vendor_id', $vendorId)
            ->findAll();

        if (! $items) {
            return $this->jsonError('Order not found for your account.', null, 404);
        }

        $order['status_label'] = OrderModel::STATUSES[$order['status']] ?? $order['status'];
        $order['items'] = $items;
        $order['vendor_total'] = array_sum(array_map(
            static fn ($row) => (float) ($row['line_total'] ?? 0),
            $items
        ));

        return $this->jsonSuccess('Order loaded.', $order);
    }
}
