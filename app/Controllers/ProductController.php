<?php

namespace App\Controllers;

use App\Models\InstallmentPlanModel;
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

        $planIds = $model->planIds((int) $product['id']);
        $plans = [];
        if ($planIds) {
            $plans = model(InstallmentPlanModel::class)
                ->whereIn('id', $planIds)
                ->where('status', 1)
                ->findAll();
        }

        $images = $product['images'] ? json_decode($product['images'], true) : [];
        $related = $model->where('status', 1)
            ->where('category_id', $product['category_id'])
            ->where('id !=', $product['id'])
            ->findAll(4);

        return $this->storeView('product', [
            'pageTitle'  => $product['name'],
            'activeMenu' => 'shop',
            'product'    => $product,
            'images'     => $images ?: ['theme/images/demos/demo22/products/1.jpg'],
            'plans'      => $plans,
            'related'    => $related,
            'cssFile'    => 'style.min.css',
        ]);
    }
}
