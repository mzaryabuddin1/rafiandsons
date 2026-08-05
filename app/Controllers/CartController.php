<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\InstallmentPlanModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class CartController extends BaseStoreController
{
    public function index()
    {
        $items = $this->cart->items();
        $planIds = [];
        foreach ($items as $item) {
            if (! empty($item['plan_id'])) {
                $planIds[] = (int) $item['plan_id'];
            }
        }

        $plans = model(InstallmentPlanModel::class)->where('status', 1)->findAll();
        $plansByProduct = [];
        foreach ($items as $item) {
            $ids = model(ProductModel::class)->planIds((int) $item['product_id']);
            $plansByProduct[$item['product_id']] = $ids
                ? model(InstallmentPlanModel::class)->whereIn('id', $ids)->where('status', 1)->findAll()
                : $plans;
        }

        return $this->storeView('cart', [
            'pageTitle'       => 'Cart',
            'activeMenu'      => 'cart',
            'items'           => $items,
            'plansByProduct'  => $plansByProduct,
            'cssFile'         => 'style.min.css',
        ]);
    }

    public function checkout()
    {
        $items = $this->cart->items();
        if (! $items) {
            return redirect()->to(site_url('cart'))->with('error', 'Your cart is empty.');
        }

        $primary = reset($items);
        $plans = [];
        if (! empty($primary['product_id'])) {
            $ids = model(ProductModel::class)->planIds((int) $primary['product_id']);
            if ($ids) {
                $plans = model(InstallmentPlanModel::class)->whereIn('id', $ids)->where('status', 1)->findAll();
            }
        }
        if (! $plans) {
            $plans = model(InstallmentPlanModel::class)->where('status', 1)->findAll();
        }

        return $this->storeView('checkout', [
            'pageTitle'  => 'Checkout',
            'activeMenu' => 'cart',
            'items'      => $items,
            'plans'      => $plans,
            'selectedPlanId' => $primary['plan_id'] ?? null,
            'cssFile'    => 'style.min.css',
        ]);
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty = (int) ($this->request->getPost('qty') ?: 1);
        $planId = $this->request->getPost('plan_id') ? (int) $this->request->getPost('plan_id') : null;

        $result = $this->cart->add($productId, $qty, $planId);
        if (! $result['success']) {
            return $this->jsonError($result['message']);
        }

        return $this->jsonSuccess($result['message'], [
            'count'    => $result['count'],
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function update()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty = (int) $this->request->getPost('qty');
        $result = $this->cart->updateQty($productId, $qty);
        if (! $result['success']) {
            return $this->jsonError($result['message']);
        }

        return $this->jsonSuccess($result['message'], $result);
    }

    public function setPlan()
    {
        $productId = (int) $this->request->getPost('product_id');
        $planId = $this->request->getPost('plan_id') ? (int) $this->request->getPost('plan_id') : null;
        $result = $this->cart->setPlan($productId, $planId);

        return $result['success']
            ? $this->jsonSuccess($result['message'])
            : $this->jsonError($result['message']);
    }

    public function remove()
    {
        $productId = (int) $this->request->getPost('product_id');
        $result = $this->cart->remove($productId);

        return $this->jsonSuccess($result['message'], [
            'count'    => $this->cart->count(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function placeOrder()
    {
        $items = $this->cart->items();
        if (! $items) {
            return $this->jsonError('Your cart is empty.');
        }

        $name = trim((string) $this->request->getPost('customer_name'));
        $phone = trim((string) $this->request->getPost('customer_phone'));
        $email = trim((string) $this->request->getPost('customer_email'));
        $cnic = trim((string) $this->request->getPost('customer_cnic'));
        $address = trim((string) $this->request->getPost('customer_address'));
        $city = trim((string) $this->request->getPost('customer_city'));
        $planId = (int) $this->request->getPost('installment_plan_id');
        $notes = trim((string) $this->request->getPost('notes'));

        if ($name === '' || $phone === '') {
            return $this->jsonError('Name and phone are required.');
        }

        if (! $planId) {
            // fallback to first item plan
            $first = reset($items);
            $planId = (int) ($first['plan_id'] ?? 0);
        }

        $plan = $planId ? model(InstallmentPlanModel::class)->where('status', 1)->find($planId) : null;
        if (! $plan) {
            return $this->jsonError('Please select an installment plan.');
        }

        $customerId = model(CustomerModel::class)->insert([
            'name'    => $name,
            'email'   => $email ?: null,
            'phone'   => $phone,
            'cnic'    => $cnic ?: null,
            'address' => $address ?: null,
            'city'    => $city ?: null,
            'notes'   => $notes ?: null,
            'status'  => 1,
        ]);

        $subtotal = $this->cart->subtotal();
        $orderNumber = 'RS-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $db = db_connect();
        $db->transStart();

        $orderId = model(OrderModel::class)->insert([
            'order_number'        => $orderNumber,
            'customer_id'         => $customerId,
            'customer_name'       => $name,
            'customer_email'      => $email,
            'customer_phone'      => $phone,
            'customer_cnic'       => $cnic,
            'customer_address'    => $address,
            'customer_city'       => $city,
            'installment_plan_id' => $plan['id'],
            'plan_name'           => $plan['name'],
            'down_payment'        => $plan['down_payment'],
            'monthly_installment' => $plan['monthly_installment'],
            'months'              => $plan['months'],
            'processing_charges'  => $plan['processing_charges'],
            'total_payable'       => $plan['total_payable'],
            'subtotal'            => $subtotal,
            'status'              => 'new',
            'admin_notes'         => $notes,
        ]);

        foreach ($items as $item) {
            model(OrderItemModel::class)->insert([
                'order_id'     => $orderId,
                'product_id'   => $item['product_id'],
                'product_name' => $item['name'],
                'sku'          => $item['sku'],
                'unit_price'   => $item['price'],
                'quantity'     => $item['qty'],
                'line_total'   => $item['price'] * $item['qty'],
            ]);
        }

        $db->transComplete();
        $this->cart->clear();

        return $this->jsonSuccess('Your installment booking request has been submitted.', [
            'order_number' => $orderNumber,
            'redirect'     => site_url('order/success/' . $orderNumber),
        ]);
    }

    public function success(string $orderNumber)
    {
        $order = model(OrderModel::class)->where('order_number', $orderNumber)->first();
        if (! $order) {
            return redirect()->to(site_url('/'));
        }

        return $this->storeView('order_success', [
            'pageTitle' => 'Order Submitted',
            'activeMenu'=> 'cart',
            'order'     => $order,
            'cssFile'   => 'style.min.css',
        ]);
    }
}
