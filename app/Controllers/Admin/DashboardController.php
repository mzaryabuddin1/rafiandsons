<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\CustomerModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        if (! $this->auth->can('dashboard.view')) {
            return redirect()->to(site_url('admin/login'));
        }

        return $this->adminView('dashboard/index', [
            'pageTitle'  => 'Dashboard',
            'activeMenu' => 'dashboard',
        ]);
    }

    public function stats()
    {
        if ($denied = $this->requirePermission('dashboard.view')) {
            return $denied;
        }

        $orderModel = model(OrderModel::class);
        $db         = db_connect();

        $recent = $orderModel->orderBy('id', 'DESC')->findAll(8);
        foreach ($recent as &$row) {
            $row['status_label'] = OrderModel::STATUSES[$row['status']] ?? $row['status'];
        }
        unset($row);

        $totalOrders = $orderModel->countAllResults(true);
        $monthly     = $this->monthlyOrderStats($db, 6);
        $statusBreak = $this->statusBreakdown($db);
        $revenueRow  = $db->table('orders')
            ->selectSum('total_payable', 'total')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        $revenueThisMonth = $this->sumRevenueForMonth($db, 0);
        $revenueLastMonth = $this->sumRevenueForMonth($db, 1);
        $ordersThisMonth  = $this->countOrdersForMonth($db, 0);
        $ordersLastMonth  = $this->countOrdersForMonth($db, 1);

        $pendingStatuses = ['new', 'under_review', 'customer_contacted', 'processing'];
        $pendingOrders   = $db->table('orders')
            ->whereIn('status', $pendingStatuses)
            ->where('deleted_at', null)
            ->countAllResults();

        return $this->jsonSuccess('Dashboard stats loaded.', [
            'counts' => [
                'products'         => model(ProductModel::class)->where('status', 1)->countAllResults(true),
                'categories'       => model(CategoryModel::class)->countAllResults(true),
                'customers'        => model(CustomerModel::class)->countAllResults(true),
                'orders'           => $totalOrders,
                'new_orders'       => $db->table('orders')->where('status', 'new')->where('deleted_at', null)->countAllResults(),
                'approved'         => $db->table('orders')->where('status', 'approved')->where('deleted_at', null)->countAllResults(),
                'completed'        => $db->table('orders')->where('status', 'completed')->where('deleted_at', null)->countAllResults(),
                'cancelled'        => $db->table('orders')->where('status', 'cancelled')->where('deleted_at', null)->countAllResults(),
                'pending'          => $pendingOrders,
                'total_revenue'    => (float) ($revenueRow['total'] ?? 0),
                'revenue_month'    => $revenueThisMonth,
                'orders_month'     => $ordersThisMonth,
                'revenue_change'   => $this->percentChange($revenueLastMonth, $revenueThisMonth),
                'orders_change'    => $this->percentChange($ordersLastMonth, $ordersThisMonth),
                'approval_rate'    => $totalOrders > 0
                    ? round((($db->table('orders')->whereIn('status', ['approved', 'completed'])->where('deleted_at', null)->countAllResults()) / $totalOrders) * 100)
                    : 0,
            ],
            'monthly'       => $monthly,
            'status_chart'  => $statusBreak,
            'recent_orders' => $recent,
        ]);
    }

    private function monthlyOrderStats($db, int $months): array
    {
        $labels  = [];
        $orders  = [];
        $revenue = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = date('Y-m-01 00:00:00', strtotime("-{$i} months"));
            $end   = date('Y-m-t 23:59:59', strtotime("-{$i} months"));
            $labels[] = date('M', strtotime($start));

            $orders[] = $db->table('orders')
                ->where('deleted_at', null)
                ->where('created_at >=', $start)
                ->where('created_at <=', $end)
                ->countAllResults();

            $sum = $db->table('orders')
                ->selectSum('total_payable', 'total')
                ->where('deleted_at', null)
                ->where('created_at >=', $start)
                ->where('created_at <=', $end)
                ->get()
                ->getRowArray();

            $revenue[] = (float) ($sum['total'] ?? 0);
        }

        return [
            'labels'  => $labels,
            'orders'  => $orders,
            'revenue' => $revenue,
        ];
    }

    private function statusBreakdown($db): array
    {
        $rows = $db->table('orders')
            ->select('status, COUNT(*) as total')
            ->where('deleted_at', null)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $chart = [];
        foreach ($rows as $row) {
            if ((int) $row['total'] === 0) {
                continue;
            }
            $chart[] = [
                'status' => $row['status'],
                'label'  => OrderModel::STATUSES[$row['status']] ?? ucfirst(str_replace('_', ' ', $row['status'])),
                'value'  => (int) $row['total'],
            ];
        }

        return $chart;
    }

    private function sumRevenueForMonth($db, int $monthsAgo): float
    {
        $start = date('Y-m-01 00:00:00', strtotime("-{$monthsAgo} months"));
        $end   = date('Y-m-t 23:59:59', strtotime("-{$monthsAgo} months"));

        $row = $db->table('orders')
            ->selectSum('total_payable', 'total')
            ->where('deleted_at', null)
            ->where('created_at >=', $start)
            ->where('created_at <=', $end)
            ->get()
            ->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    private function countOrdersForMonth($db, int $monthsAgo): int
    {
        $start = date('Y-m-01 00:00:00', strtotime("-{$monthsAgo} months"));
        $end   = date('Y-m-t 23:59:59', strtotime("-{$monthsAgo} months"));

        return $db->table('orders')
            ->where('deleted_at', null)
            ->where('created_at >=', $start)
            ->where('created_at <=', $end)
            ->countAllResults();
    }

    private function percentChange(float $previous, float $current): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
