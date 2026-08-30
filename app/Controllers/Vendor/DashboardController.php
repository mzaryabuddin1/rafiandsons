<?php

namespace App\Controllers\Vendor;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class DashboardController extends BaseVendorController
{
    public function index()
    {
        $vendorId = (int) $this->auth->id();

        $productCount = model(ProductModel::class)
            ->where('vendor_id', $vendorId)
            ->where('status', 1)
            ->countAllResults();

        $itemRows = model(OrderItemModel::class)
            ->where('vendor_id', $vendorId)
            ->findAll();

        $orderIds = array_values(array_unique(array_map('intval', array_column($itemRows, 'order_id'))));
        $orderCount = count($orderIds);
        $salesTotal = array_sum(array_map(
            static fn ($row) => (float) ($row['line_total'] ?? 0),
            $itemRows
        ));

        $recentOrders = [];
        if ($orderIds) {
            $orders = model(OrderModel::class)
                ->whereIn('id', $orderIds)
                ->orderBy('id', 'DESC')
                ->findAll(5);

            $itemsByOrder = [];
            foreach ($itemRows as $item) {
                $itemsByOrder[(int) $item['order_id']][] = $item;
            }

            foreach ($orders as $order) {
                $vendorItems = $itemsByOrder[(int) $order['id']] ?? [];
                $recentOrders[] = [
                    'order_number'  => $order['order_number'],
                    'customer_name' => $order['customer_name'],
                    'status_label'  => OrderModel::STATUSES[$order['status']] ?? $order['status'],
                    'vendor_total'  => array_sum(array_map(
                        static fn ($row) => (float) ($row['line_total'] ?? 0),
                        $vendorItems
                    )),
                    'created_at'    => $order['created_at'] ?? '',
                    'id'            => $order['id'],
                ];
            }
        }

        return $this->vendorView('dashboard/index', [
            'pageTitle'    => 'Dashboard',
            'activeMenu'   => 'dashboard',
            'productCount' => $productCount,
            'orderCount'   => $orderCount,
            'salesTotal'   => $salesTotal,
            'recentOrders' => $recentOrders,
        ]);
    }
}
