<?php

namespace App\Libraries;

use App\Models\ProductModel;

class CartService
{
    public const SESSION_KEY = 'store_cart';

    public function items(): array
    {
        $items = session()->get(self::SESSION_KEY) ?? [];

        return $this->normalizeItems($items);
    }

    public function count(): int
    {
        $count = 0;
        foreach ($this->items() as $item) {
            $count += (int) $item['qty'];
        }

        return $count;
    }

    public function subtotal(): float
    {
        $total = 0;
        foreach ($this->items() as $item) {
            $total += ((float) $item['price']) * ((int) $item['qty']);
        }

        return round($total, 2);
    }

    public function grandTotal(): float
    {
        $total = 0;
        foreach ($this->items() as $item) {
            $qty = (int) $item['qty'];
            if (($item['payment_type'] ?? 'cash') === 'installment') {
                $total += ((float) ($item['total_payable'] ?? 0)) * $qty;
            } else {
                $total += ((float) ($item['cash_price'] ?? $item['price'])) * $qty;
            }
        }

        return round($total, 2);
    }

    public function hasInstallmentItems(): bool
    {
        foreach ($this->items() as $item) {
            if (($item['payment_type'] ?? '') === 'installment') {
                return true;
            }
        }

        return false;
    }

    public function hasCashItems(): bool
    {
        foreach ($this->items() as $item) {
            if (($item['payment_type'] ?? 'cash') === 'cash') {
                return true;
            }
        }

        return false;
    }

    public function orderPaymentType(): string
    {
        $hasCash = $this->hasCashItems();
        $hasInst = $this->hasInstallmentItems();

        if ($hasCash && $hasInst) {
            return 'mixed';
        }

        return $hasInst ? 'installment' : 'cash';
    }

    public function add(int $productId, int $qty = 1, string $paymentType = 'cash', ?int $planId = null): array
    {
        $product = model(ProductModel::class)
            ->where('id', $productId)
            ->where('status', 1)
            ->first();
        if (! $product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        $cashOk = (int) ($product['cash_available'] ?? 1) === 1;
        $instOk = (int) ($product['installment_available'] ?? 0) === 1;
        $paymentType = $paymentType === 'installment' ? 'installment' : 'cash';

        if ($paymentType === 'cash' && ! $cashOk && $instOk) {
            $paymentType = 'installment';
        }
        if ($paymentType === 'installment' && ! $instOk && $cashOk) {
            $paymentType = 'cash';
        }

        if ($paymentType === 'cash' && ! $cashOk) {
            return ['success' => false, 'message' => 'This product is not available for cash purchase.'];
        }
        if ($paymentType === 'installment' && ! $instOk) {
            return ['success' => false, 'message' => 'This product is not available on installment.'];
        }

        $plan = null;
        if ($paymentType === 'installment') {
            if (! $planId) {
                $plans = model(ProductModel::class)->plansForProduct($productId);
                if ($plans === []) {
                    return ['success' => false, 'message' => 'No installment plans available for this product.'];
                }
                $plan = $plans[0];
                $planId = (int) $plan['id'];
            } else {
                $plan = model(ProductModel::class)->resolvePlan($productId, $planId);
                if (! $plan) {
                    return ['success' => false, 'message' => 'Invalid installment plan selected.'];
                }
            }
        }

        $qty = max(1, $qty);
        $cartKey = $this->makeCartKey($productId, $paymentType, $planId);
        $items = $this->items();
        $newItem = $this->buildItem($product, $qty, $paymentType, $plan, $cartKey);

        if (isset($items[$cartKey])) {
            $items[$cartKey]['qty'] += $qty;
        } else {
            $items[$cartKey] = $newItem;
        }

        session()->set(self::SESSION_KEY, $items);

        return ['success' => true, 'message' => 'Added to cart.', 'count' => $this->count()];
    }

    public function updateQty(string $cartKey, int $qty): array
    {
        $items = $this->items();
        if (! isset($items[$cartKey])) {
            return ['success' => false, 'message' => 'Item not in cart.'];
        }

        if ($qty <= 0) {
            unset($items[$cartKey]);
        } else {
            $items[$cartKey]['qty'] = $qty;
        }

        session()->set(self::SESSION_KEY, $items);

        return [
            'success'     => true,
            'message'     => 'Cart updated.',
            'count'       => $this->count(),
            'subtotal'    => $this->subtotal(),
            'grand_total' => $this->grandTotal(),
        ];
    }

    public function setPlan(string $cartKey, ?int $planId): array
    {
        $items = $this->items();
        if (! isset($items[$cartKey])) {
            return ['success' => false, 'message' => 'Item not in cart.'];
        }

        $item = $items[$cartKey];
        if (($item['payment_type'] ?? '') !== 'installment') {
            return ['success' => false, 'message' => 'This item is not on installment.'];
        }

        if (! $planId) {
            return ['success' => false, 'message' => 'Please select a plan.'];
        }

        $productId = (int) $item['product_id'];
        $plan = model(ProductModel::class)->resolvePlan($productId, $planId);
        if (! $plan) {
            return ['success' => false, 'message' => 'Invalid plan.'];
        }

        $newKey = $this->makeCartKey($productId, 'installment', $planId);
        $updated = array_merge($item, $this->planSnapshot($plan));
        $updated['price'] = (float) $plan['down_payment'];
        $updated['cart_key'] = $newKey;

        unset($items[$cartKey]);

        if (isset($items[$newKey])) {
            $items[$newKey]['qty'] += (int) $updated['qty'];
        } else {
            $items[$newKey] = $updated;
        }

        session()->set(self::SESSION_KEY, $items);

        return [
            'success'  => true,
            'message'  => 'Plan updated.',
            'subtotal' => $this->subtotal(),
            'item'     => $items[$newKey],
            'cart_key' => $newKey,
        ];
    }

    public function setPaymentType(string $cartKey, string $paymentType, ?int $planId = null): array
    {
        $items = $this->items();
        if (! isset($items[$cartKey])) {
            return ['success' => false, 'message' => 'Item not in cart.'];
        }

        $item = $items[$cartKey];
        $productId = (int) $item['product_id'];
        $product = model(ProductModel::class)->where('id', $productId)->where('status', 1)->first();
        if (! $product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        $qty = (int) $item['qty'];
        $paymentType = $paymentType === 'installment' ? 'installment' : 'cash';

        $plan = null;
        if ($paymentType === 'installment') {
            if (! $planId) {
                $planId = (int) ($item['plan_id'] ?? 0);
            }
            if (! $planId) {
                $plans = model(ProductModel::class)->plansForProduct($productId);
                if ($plans === []) {
                    return ['success' => false, 'message' => 'No installment plans available.'];
                }
                $plan = $plans[0];
                $planId = (int) $plan['id'];
            } else {
                $plan = model(ProductModel::class)->resolvePlan($productId, $planId);
                if (! $plan) {
                    return ['success' => false, 'message' => 'Invalid plan.'];
                }
            }
        }

        $newKey = $this->makeCartKey($productId, $paymentType, $planId);
        $updated = $this->buildItem($product, $qty, $paymentType, $plan, $newKey);

        unset($items[$cartKey]);

        if (isset($items[$newKey])) {
            $items[$newKey]['qty'] += $qty;
        } else {
            $items[$newKey] = $updated;
        }

        session()->set(self::SESSION_KEY, $items);

        return [
            'success'  => true,
            'message'  => 'Payment option updated.',
            'subtotal' => $this->subtotal(),
            'item'     => $items[$newKey],
            'cart_key' => $newKey,
        ];
    }

    public function remove(string $cartKey): array
    {
        return $this->updateQty($cartKey, 0);
    }

    /** @deprecated Use remove(string $cartKey) */
    public function removeByProductId(int $productId): array
    {
        foreach (array_keys($this->items()) as $key) {
            if ((int) ($this->items()[$key]['product_id'] ?? 0) === $productId) {
                return $this->remove($key);
            }
        }

        return ['success' => false, 'message' => 'Item not in cart.'];
    }

    public function clear(): void
    {
        session()->remove(self::SESSION_KEY);
    }

    public function makeCartKey(int $productId, string $paymentType, ?int $planId = null): string
    {
        if ($paymentType === 'installment') {
            return $productId . ':i:' . (int) $planId;
        }

        return $productId . ':c';
    }

    /**
     * Migrate legacy session rows keyed only by product_id.
     *
     * @param array<string, array<string, mixed>> $items
     * @return array<string, array<string, mixed>>
     */
    private function normalizeItems(array $items): array
    {
        $normalized = [];
        $changed = false;

        foreach ($items as $key => $item) {
            if (! is_array($item)) {
                $changed = true;
                continue;
            }

            $productId = (int) ($item['product_id'] ?? 0);
            if ($productId <= 0) {
                $changed = true;
                continue;
            }

            $paymentType = ($item['payment_type'] ?? 'cash') === 'installment' ? 'installment' : 'cash';
            $planId = $paymentType === 'installment' ? (int) ($item['plan_id'] ?? 0) : null;
            $cartKey = $item['cart_key'] ?? $this->makeCartKey($productId, $paymentType, $planId);

            if ($cartKey !== $key || empty($item['cart_key'])) {
                $changed = true;
            }

            $item['cart_key'] = $cartKey;

            if (isset($normalized[$cartKey])) {
                $normalized[$cartKey]['qty'] += (int) ($item['qty'] ?? 1);
                $changed = true;
            } else {
                $normalized[$cartKey] = $item;
            }
        }

        if ($changed) {
            session()->set(self::SESSION_KEY, $normalized);
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $product
     * @param array<string, mixed>|null $plan
     * @return array<string, mixed>
     */
    private function buildItem(array $product, int $qty, string $paymentType, ?array $plan, string $cartKey): array
    {
        $images = $product['images'] ? json_decode($product['images'], true) : [];
        $cashPrice = (float) $product['price'];

        $item = [
            'cart_key'     => $cartKey,
            'product_id'   => (int) $product['id'],
            'name'         => $product['name'],
            'slug'         => $product['slug'],
            'sku'          => $product['sku'],
            'cash_price'   => $cashPrice,
            'image'        => $images[0] ?? 'theme/images/demos/demo22/products/1.jpg',
            'qty'          => $qty,
            'payment_type' => $paymentType,
        ];

        if ($paymentType === 'installment' && $plan) {
            $item = array_merge($item, $this->planSnapshot($plan));
            $item['price'] = (float) $plan['down_payment'];
        } else {
            $item['price']             = $cashPrice;
            $item['plan_id']           = null;
            $item['plan_name']         = null;
            $item['down_payment']      = null;
            $item['monthly_installment'] = null;
            $item['months']            = null;
            $item['total_payable']     = null;
        }

        return $item;
    }

    /**
     * @param array<string, mixed> $plan
     * @return array<string, mixed>
     */
    private function planSnapshot(array $plan): array
    {
        return [
            'plan_id'             => (int) $plan['id'],
            'plan_name'           => $plan['name'],
            'down_payment'        => (float) $plan['down_payment'],
            'monthly_installment' => (float) $plan['monthly_installment'],
            'months'              => (int) $plan['months'],
            'total_payable'       => (float) $plan['total_payable'],
        ];
    }
}
