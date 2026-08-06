<?php

namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Models\OrderModel;

class TrackOrderController extends BaseStoreController
{
    public function index()
    {
        $orderNumber = trim((string) $this->request->getGet('order'));

        return $this->storeView('track_order', [
            'pageTitle'    => 'Track Your Order',
            'activeMenu'   => 'track',
            'prefillOrder' => $orderNumber,
            'bodyClass'    => 'store-qist',
            'cssFile'      => 'demo22.min.css',
        ]);
    }

    public function lookup()
    {
        $orderNumber = strtoupper(trim((string) $this->request->getPost('order_number')));
        $phone = trim((string) $this->request->getPost('phone'));

        if ($orderNumber === '' && $phone === '') {
            return $this->jsonError('Enter an order number or phone number to track.');
        }

        $model = model(OrderModel::class);
        $orders = [];

        if ($orderNumber !== '') {
            $order = $model->where('order_number', $orderNumber)->first();
            if (! $order) {
                return $this->jsonError('No order found with this order number.');
            }
            $orders = [$order];
        } else {
            $orders = $this->findOrdersByPhone($phone);
            if ($orders === []) {
                return $this->jsonError('No orders found for this phone number.');
            }
        }

        $payload = [];
        foreach ($orders as $order) {
            $statusKey = (string) ($order['status'] ?? 'processing');
            if ($statusKey === 'new') {
                $statusKey = 'processing';
            }
            $statusLabel = OrderModel::STATUSES[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
            $items = model(OrderItemModel::class)->where('order_id', $order['id'])->findAll();

            $payload[] = [
                'order' => [
                    'order_number'   => $order['order_number'],
                    'customer_name'  => $order['customer_name'],
                    'customer_phone' => $this->maskPhone((string) $order['customer_phone']),
                    'status'         => $statusKey,
                    'status_label'   => $statusLabel,
                    'payment_type'   => ucfirst((string) ($order['payment_type'] ?? 'cash')),
                    'subtotal'       => (float) ($order['subtotal'] ?? 0),
                    'total_payable'  => (float) ($order['total_payable'] ?? 0),
                    'created_at'     => $order['created_at'] ? date('d M Y, h:i A', strtotime($order['created_at'])) : '',
                    'city'           => $order['customer_city'] ?? '',
                ],
                'items' => array_map(static function (array $item) {
                    return [
                        'name'    => $item['product_name'] ?? 'Product',
                        'qty'     => (int) ($item['quantity'] ?? 1),
                        'payment' => ucfirst((string) ($item['payment_type'] ?? 'cash')),
                        'plan'    => $item['plan_name'] ?? '',
                        'due_now' => (float) ($item['line_total'] ?? 0),
                    ];
                }, $items),
            ];
        }

        return $this->jsonSuccess(
            count($payload) === 1 ? 'Order found.' : count($payload) . ' orders found.',
            ['orders' => $payload]
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function findOrdersByPhone(string $phone): array
    {
        $inputDigits = preg_replace('/\D+/', '', $phone) ?? '';
        if (strlen($inputDigits) < 7) {
            return [];
        }

        $tail = substr($inputDigits, -7);
        $candidates = model(OrderModel::class)
            ->like('customer_phone', $tail)
            ->orderBy('id', 'DESC')
            ->findAll(30);

        $matched = [];
        foreach ($candidates as $order) {
            $orderDigits = preg_replace('/\D+/', '', (string) ($order['customer_phone'] ?? '')) ?? '';
            if ($this->phonesMatch($orderDigits, $inputDigits)) {
                $matched[] = $order;
            }
        }

        return $matched;
    }

    private function phonesMatch(string $orderDigits, string $inputDigits): bool
    {
        if ($orderDigits === '' || $inputDigits === '') {
            return false;
        }

        return $orderDigits === $inputDigits
            || str_contains($orderDigits, $inputDigits)
            || str_contains($inputDigits, $orderDigits)
            || (strlen($inputDigits) >= 7 && str_ends_with($orderDigits, substr($inputDigits, -7)));
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (strlen($digits) < 4) {
            return '****';
        }

        return str_repeat('*', max(0, strlen($digits) - 4)) . substr($digits, -4);
    }
}
