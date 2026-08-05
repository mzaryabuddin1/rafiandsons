<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ProductController extends BaseStoreController
{
    public function show(string $slug)
    {
        $model = model(ProductModel::class);
        $product = $model->where('slug', $slug)->where('status', 1)->first();
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Product not found');
        }

        $plans = (int) ($product['installment_available'] ?? 0) === 1
            ? $model->plansForProduct((int) $product['id'])
            : [];
        $images = $product['images'] ? json_decode($product['images'], true) : [];
        $related = $this->enrichProducts(
            $model->where('status', 1)
                ->where('category_id', $product['category_id'])
                ->where('id !=', $product['id'])
                ->findAll(4)
        );

        return $this->storeView('product', [
            'pageTitle'  => $product['name'],
            'activeMenu' => 'shop',
            'product'    => $product,
            'images'     => $images ?: ['theme/images/demos/demo22/products/1.jpg'],
            'plans'      => $plans,
            'related'    => $related,
            'bodyClass'  => 'store-qist',
            'cssFile'    => 'demo22.min.css',
        ]);
    }

    public function quick(string $slug)
    {
        $model = model(ProductModel::class);
        $product = $model->where('slug', $slug)->where('status', 1)->first();
        if (! $product) {
            return $this->jsonError('Product not found.', null, 404);
        }

        $enriched = $this->enrichProducts([$product])[0];
        $images = $product['images'] ? json_decode($product['images'], true) : [];
        $plans = (int) ($product['installment_available'] ?? 0) === 1
            ? $model->plansForProduct((int) $product['id'])
            : [];

        return $this->jsonSuccess('OK', [
            'id'                    => (int) $product['id'],
            'name'                  => $product['name'],
            'slug'                  => $product['slug'],
            'sku'                   => $product['sku'],
            'price'                 => (float) $product['price'],
            'compare_price'         => ! empty($product['compare_price']) ? (float) $product['compare_price'] : null,
            'cash_available'        => (int) ($product['cash_available'] ?? 1),
            'installment_available' => (int) ($product['installment_available'] ?? 0),
            'min_advance'           => $enriched['min_advance'] ?? null,
            'description' => $product['description'],
            'url'         => site_url('product/' . $product['slug']),
            'image'       => $this->productImage($product['images']),
            'images'      => array_map(static fn ($img) => base_url($img), $images ?: ['theme/images/demos/demo22/products/1.jpg']),
            'plans'       => $plans,
            'stock'       => $product['stock_status'] === 'in_stock' ? 'In Stock' : 'Out of Stock',
        ]);
    }
}
