<?php

namespace App\Controllers;

use App\Libraries\OrderMailService;
use App\Libraries\StoreAuth;
use App\Models\BankAccountModel;
use App\Models\CustomerModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class CartController extends BaseStoreController
{
    public function index()
    {
        $items = $this->cart->items();
        $productModel = model(ProductModel::class);

        foreach ($items as $key => $item) {
            $pid = (int) $item['product_id'];
            $product = $productModel->find($pid);
            if ($product) {
                $items[$key]['name'] = $product['name'];
                $items[$key]['slug'] = $product['slug'];
                $items[$key]['cash_available'] = (int) ($product['cash_available'] ?? 1);
                $items[$key]['installment_available'] = (int) ($product['installment_available'] ?? 0);
                $items[$key]['compare_price'] = $product['compare_price'] ?? null;
                if (! isset($items[$key]['cash_price'])) {
                    $items[$key]['cash_price'] = (float) $product['price'];
                }
            }
        }

        return $this->storeView('cart', [
            'pageTitle'      => 'Shopping Cart',
            'activeMenu'     => 'cart',
            'items'          => $items,
            'cartGrandTotal' => $this->cart->grandTotal(),
            'bodyClass'      => 'store-qist',
            'cssFile'        => 'demo22.min.css',
        ]);
    }

    public function checkout()
    {
        $items = $this->cart->items();
        if (! $items) {
            return redirect()->to(site_url('cart'))->with('error', 'Your cart is empty.');
        }

        foreach ($items as $item) {
            if (($item['payment_type'] ?? '') === 'installment' && empty($item['plan_id'])) {
                return redirect()->to(site_url('cart'))->with('error', 'Please select an installment plan for all items.');
            }
        }

        return $this->storeView('checkout', [
            'pageTitle'        => 'Checkout',
            'activeMenu'       => 'cart',
            'items'            => $items,
            'hasInstallment'   => $this->cart->hasInstallmentItems(),
            'hasCash'          => $this->cart->hasCashItems(),
            'orderPaymentType' => $this->cart->orderPaymentType(),
            'cartGrandTotal'   => $this->cart->grandTotal(),
            'checkoutCustomer' => $this->checkoutCustomerData(),
            'bankAccounts'     => model(BankAccountModel::class)->activeAccounts(),
            'isLoggedIn'       => (new StoreAuth())->check(),
            'bodyClass'        => 'store-qist',
            'cssFile'          => 'demo22.min.css',
        ]);
    }

    protected function checkoutCustomerData(): array
    {
        $auth = new StoreAuth();
        if (! $auth->check()) {
            return [];
        }

        $customer = model(CustomerModel::class)->find($auth->id());

        return is_array($customer) ? $customer : ($auth->user() ?? []);
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty = (int) ($this->request->getPost('qty') ?: 1);
        $paymentType = trim((string) $this->request->getPost('payment_type'));
        $planId = $this->request->getPost('plan_id') ? (int) $this->request->getPost('plan_id') : null;

        if ($paymentType === '' && $planId) {
            $paymentType = 'installment';
        }
        if ($paymentType === '') {
            $paymentType = 'cash';
        }

        $result = $this->cart->add($productId, $qty, $paymentType, $planId);
        if (! $result['success']) {
            return $this->jsonError($result['message']);
        }

        return $this->jsonSuccess($result['message'], [
            'count'    => $result['count'],
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    private function cartKeyFromRequest(): string
    {
        return trim((string) ($this->request->getPost('cart_key') ?: $this->request->getPost('product_id')));
    }

    public function update()
    {
        $result = $this->cart->updateQty($this->cartKeyFromRequest(), (int) $this->request->getPost('qty'));
        if (! $result['success']) {
            return $this->jsonError($result['message']);
        }

        return $this->jsonSuccess($result['message'], $result);
    }

    public function setPlan()
    {
        $planId = $this->request->getPost('plan_id') ? (int) $this->request->getPost('plan_id') : null;
        $result = $this->cart->setPlan($this->cartKeyFromRequest(), $planId);

        return $result['success']
            ? $this->jsonSuccess($result['message'], [
                'subtotal' => $result['subtotal'] ?? $this->cart->subtotal(),
                'item'     => $result['item'] ?? null,
                'cart_key' => $result['cart_key'] ?? null,
            ])
            : $this->jsonError($result['message']);
    }

    public function setPayment()
    {
        $paymentType = trim((string) $this->request->getPost('payment_type'));
        $planId = $this->request->getPost('plan_id') ? (int) $this->request->getPost('plan_id') : null;
        $result = $this->cart->setPaymentType($this->cartKeyFromRequest(), $paymentType, $planId);

        return $result['success']
            ? $this->jsonSuccess($result['message'], [
                'subtotal' => $result['subtotal'] ?? $this->cart->subtotal(),
                'item'     => $result['item'] ?? null,
                'cart_key' => $result['cart_key'] ?? null,
            ])
            : $this->jsonError($result['message']);
    }

    public function remove()
    {
        $result = $this->cart->remove($this->cartKeyFromRequest());

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
        $notes = trim((string) $this->request->getPost('notes'));

        if ($name === '' || $phone === '') {
            return $this->jsonError('Name and phone are required.');
        }

        $receiptImage = null;
        $receiptFile = $this->request->getFile('receipt_image');
        if ($receiptFile && $receiptFile->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $receiptFile->isValid() || $receiptFile->hasMoved()) {
                return $this->jsonError('Invalid receipt file. Please upload a valid image.');
            }

            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (! in_array($receiptFile->getMimeType(), $allowed, true)) {
                return $this->jsonError('Receipt must be an image (JPG, PNG, WEBP, or GIF).');
            }

            if ($receiptFile->getSize() > 5 * 1024 * 1024) {
                return $this->jsonError('Receipt image must be 5MB or smaller.');
            }

            $target = FCPATH . 'uploads/receipts';
            if (! is_dir($target)) {
                mkdir($target, 0755, true);
            }

            $nameFile = $receiptFile->getRandomName();
            $receiptFile->move($target, $nameFile);
            $receiptImage = 'uploads/receipts/' . $nameFile;
        }

        foreach ($items as $item) {
            if (($item['payment_type'] ?? '') === 'installment' && empty($item['plan_id'])) {
                return $this->jsonError('Please select an installment plan for: ' . ($item['name'] ?? 'item'));
            }
        }

        $customerModel = model(CustomerModel::class);
        $auth = new StoreAuth();
        $customerPayload = [
            'name'    => $name,
            'email'   => $email ?: null,
            'phone'   => $phone,
            'cnic'    => $cnic ?: null,
            'address' => $address ?: null,
            'city'    => $city ?: null,
            'notes'   => $notes ?: null,
            'status'  => 1,
        ];

        if ($auth->check()) {
            $customerId = $auth->id();
            $customerModel->update($customerId, $customerPayload);
            $auth->refreshFromDb();
        } else {
            $existing = $customerModel->where('phone', $phone)->first();
            if ($existing) {
                $customerModel->update($existing['id'], $customerPayload);
                $customerId = (int) $existing['id'];
            } else {
                $customerId = $customerModel->insert($customerPayload);
            }
        }

        $dueNow = $this->cart->subtotal();
        $grandTotal = $this->cart->grandTotal();
        $paymentType = $this->cart->orderPaymentType();
        $orderNumber = 'RS-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $primaryInstallment = null;
        foreach ($items as $item) {
            if (($item['payment_type'] ?? '') === 'installment') {
                $primaryInstallment = $item;
                break;
            }
        }

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
            'payment_type'        => $paymentType,
            'installment_plan_id' => $primaryInstallment['plan_id'] ?? null,
            'plan_name'           => $primaryInstallment['plan_name'] ?? null,
            'down_payment'        => $primaryInstallment['down_payment'] ?? 0,
            'monthly_installment' => $primaryInstallment['monthly_installment'] ?? 0,
            'months'              => $primaryInstallment['months'] ?? 0,
            'processing_charges'  => 0,
            'total_payable'       => $grandTotal,
            'subtotal'            => $dueNow,
            'status'              => 'processing',
            'admin_notes'         => $notes,
            'receipt_image'       => $receiptImage,
            'payment_verified'    => 0,
            'payment_verified_at' => null,
        ]);

        $orderItems = [];
        foreach ($items as $item) {
            $isInstallment = ($item['payment_type'] ?? '') === 'installment';
            $row = [
                'order_id'     => $orderId,
                'product_id'   => $item['product_id'],
                'product_name' => $item['name'],
                'sku'          => $item['sku'],
                'payment_type' => $isInstallment ? 'installment' : 'cash',
                'cash_price'   => (float) ($item['cash_price'] ?? $item['price']),
                'unit_price'   => (float) $item['price'],
                'quantity'     => $item['qty'],
                'line_total'   => $item['price'] * $item['qty'],
            ];

            if ($isInstallment) {
                $row['installment_plan_id'] = $item['plan_id'];
                $row['plan_name']           = $item['plan_name'];
                $row['down_payment']        = $item['down_payment'];
                $row['monthly_installment'] = $item['monthly_installment'];
                $row['months']              = $item['months'];
                $row['total_payable']       = $item['total_payable'];
            }

            model(OrderItemModel::class)->insert($row);
            $orderItems[] = $row;
        }

        $db->transComplete();

        $order = model(OrderModel::class)->find($orderId);
        try {
            (new OrderMailService())->sendOrderEmails($order, $orderItems);
        } catch (\Throwable $e) {
            log_message('error', 'Order email error: ' . $e->getMessage());
        }

        $this->cart->clear();

        $message = $paymentType === 'cash'
            ? 'Your order has been placed successfully.'
            : 'Your installment booking request has been submitted.';

        return $this->jsonSuccess($message, [
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

        $orderItems = model(OrderItemModel::class)->where('order_id', $order['id'])->findAll();

        return $this->storeView('order_success', [
            'pageTitle'  => 'Order Submitted',
            'activeMenu' => 'cart',
            'order'      => $order,
            'orderItems' => $orderItems,
            'bodyClass'  => 'store-qist',
            'cssFile'    => 'demo22.min.css',
        ]);
    }
}
