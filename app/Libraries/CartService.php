<?php

namespace App\Libraries;

use App\Models\ProductModel;

class CartService
{
    public const SESSION_KEY = 'store_cart';

    public function items(): array
    {
        return session()->get(self::SESSION_KEY) ?? [];
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

    public function add(int $productId, int $qty = 1, ?int $planId = null): array
    {
        $product = model(ProductModel::class)
            ->where('id', $productId)
            ->where('status', 1)
            ->first();
        if (! $product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        $qty = max(1, $qty);
        $items = $this->items();
        $key = (string) $productId;

        if (isset($items[$key])) {
            $items[$key]['qty'] += $qty;
            if ($planId) {
                $items[$key]['plan_id'] = $planId;
            }
        } else {
            $images = $product['images'] ? json_decode($product['images'], true) : [];
            $items[$key] = [
                'product_id' => (int) $product['id'],
                'name'       => $product['name'],
                'slug'       => $product['slug'],
                'sku'        => $product['sku'],
                'price'      => (float) $product['price'],
                'image'      => $images[0] ?? 'theme/images/demos/demo22/products/1.jpg',
                'qty'        => $qty,
                'plan_id'    => $planId,
            ];
        }

        session()->set(self::SESSION_KEY, $items);

        return ['success' => true, 'message' => 'Added to cart.', 'count' => $this->count()];
    }

    public function updateQty(int $productId, int $qty): array
    {
        $items = $this->items();
        $key = (string) $productId;
        if (! isset($items[$key])) {
            return ['success' => false, 'message' => 'Item not in cart.'];
        }

        if ($qty <= 0) {
            unset($items[$key]);
        } else {
            $items[$key]['qty'] = $qty;
        }

        session()->set(self::SESSION_KEY, $items);

        return ['success' => true, 'message' => 'Cart updated.', 'count' => $this->count(), 'subtotal' => $this->subtotal()];
    }

    public function setPlan(int $productId, ?int $planId): array
    {
        $items = $this->items();
        $key = (string) $productId;
        if (! isset($items[$key])) {
            return ['success' => false, 'message' => 'Item not in cart.'];
        }

        $items[$key]['plan_id'] = $planId;
        session()->set(self::SESSION_KEY, $items);

        return ['success' => true, 'message' => 'Plan selected.'];
    }

    public function remove(int $productId): array
    {
        return $this->updateQty($productId, 0);
    }

    public function clear(): void
    {
        session()->remove(self::SESSION_KEY);
    }
}
